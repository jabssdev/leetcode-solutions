# Intersection of Two Arrays — Technical Notes

> **LeetCode #349** · Difficulty: Easy · Topic: Array, Hash Table, Two Pointers, Sorting

---

## Análisis de Complejidad

Donde `n = nums1.length` y `m = nums2.length`:

| Dimensión   | Notación   | Justificación                                                                                                                                                                                                                                                                                                        |
| ----------- | ---------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n + m)` | Dos pasadas lineales independientes: la primera recorre `nums1` para construir el set de lookup (`O(n)`); la segunda recorre `nums2` para consultar membresía y registrar coincidencias (`O(m)`). Las operaciones `has/delete` en un Set (JS) y `isset/unset` sobre un array asociativo (PHP) son `O(1)` amortizado. |
| **Espacio** | `O(n)`     | La estructura de lookup `set1` almacena hasta `n` elementos únicos de `nums1`. El array `result` almacena hasta `min(n, m)` elementos en el peor caso, pero está acotado por `O(n)`. No se usa Call Stack adicional (solución iterativa pura).                                                                       |

---

## Intuición y Enfoque

### Técnica: Hash Set — Membership Lookup con Deduplicación por Borrado

La fuerza bruta compararía cada elemento de `nums2` contra cada elemento de `nums1`: dos bucles anidados con costo `O(n·m)`, impracticable cuando ambos arrays son grandes.

La solución óptima transforma el problema de búsqueda `O(n)` por elemento en una búsqueda `O(1)` por elemento, usando una **tabla hash como índice de membresía**:

**Fase 1 — Indexación:** Se vuelcan todos los elementos de `nums1` en una estructura de conjunto (`Set` / array asociativo). Esto garantiza unicidad implícita: si `nums1` tiene duplicados, el Set colapsa todas sus copias en una sola entrada.

**Fase 2 — Intersección con deduplicación:** Se itera sobre `nums2`. Por cada elemento, se consulta si existe en `set1` en `O(1)`. Si existe, se agrega al resultado **y se elimina inmediatamente de `set1`**. Esta eliminación (`delete` / `unset`) es el mecanismo de deduplicación: garantiza que aunque `nums2` contenga duplicados del mismo valor, ese valor solo ingrese al resultado una vez, cumpliendo la restricción de que la intersección debe contener elementos únicos.

**Invariante:** Al finalizar, `result` contiene exactamente los valores que aparecen en ambos arrays, sin repeticiones, en el orden en que fueron encontrados en `nums2`.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- `new Set(nums1)` construye el conjunto directamente desde el array en una sola expresión. El constructor de `Set` acepta cualquier iterable y maneja la deduplicación internamente, eliminando la necesidad de un bucle explícito para la fase de indexación. Es la forma más idiomática y concisa en JS.
- `set1.has(num)` realiza la búsqueda de membresía en `O(1)` amortizado, respaldada por la implementación interna de hash table del motor V8.
- `set1.delete(num)` elimina el elemento del `Set` en `O(1)`, garantizando que futuras apariciones del mismo valor en `nums2` no vuelvan a calificar. Devuelve `true` si el elemento existía, aunque en este contexto su valor de retorno no se utiliza.
- `result.push(num)` es el método estándar para append en arrays JS, con costo amortizado `O(1)` gracias al modelo de crecimiento dinámico interno del motor.
- El bucle `for...of` itera sobre los valores del array (no índices), siendo el idioma más limpio para recorrido secuencial cuando el índice no es necesario.

### PHP

- PHP no tiene una clase `Set` nativa. La solución lo **simula con un array asociativo**: `$set1[$num] = true` usa el propio valor numérico como **clave** del array, aprovechando que en PHP las claves de arrays son hasheadas internamente. Esto logra lookup `O(1)` equivalente al `Set` de JS.
- `isset($set1[$num])` es la función idiomática de PHP para verificar si una clave existe **y** su valor no es `null`. Es más eficiente que `array_key_exists()` en la mayoría de los casos, ya que es una construcción del lenguaje (no una llamada a función) y realiza la consulta directamente en la tabla hash interna del array.
- `unset($set1[$num])` elimina la clave del array asociativo en `O(1)`, replicando el comportamiento de `Set.delete()`. PHP libera el slot de la tabla hash internamente.
- La fase de indexación requiere un `foreach` explícito (`foreach ($nums1 as $num) { $set1[$num] = true; }`), a diferencia de JS donde `new Set(nums1)` lo encapsula. No existe un equivalente directo a la construcción de Set desde iterable en una sola expresión en PHP estándar.
- `$result[] = $num` es el append dinámico idiomático de PHP, equivalente a `array_push` pero sin overhead de llamada a función.

---

## Lecciones Clave

- **Patrón "Hash Set como índice de membresía":** Cada vez que un problema requiera determinar si elementos de una colección existen en otra, la respuesta canónica es volcar la colección más pequeña en un Set y consultar membresía en `O(1)`. Este patrón es la base de operaciones como `JOIN` en bases de datos (hash join), detección de duplicados, validación de permisos contra listas blancas/negras, y reconciliación de datasets. La elección de qué array convertir en Set (siempre el más pequeño cuando los tamaños difieren) es una optimización de espacio relevante en producción.

- **Deduplicación por borrado vs. por estructura:** Usar `delete`/`unset` para eliminar un elemento después de procesarlo es una técnica de deduplicación **lazy** sin costo adicional de espacio. Contrasta con el enfoque alternativo de usar un segundo Set para rastrear elementos ya vistos en el resultado. Preferir el borrado sobre la creación de estructuras adicionales es una instancia del principio de diseño de **minimizar el estado mutable**: menos estructuras de datos activas implica menos superficie de error y menor presión sobre el garbage collector.
