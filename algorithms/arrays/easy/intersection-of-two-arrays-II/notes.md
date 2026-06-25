# Intersection of Two Arrays II — Technical Notes

> **LeetCode #350** · Difficulty: Easy · Topic: Array, Hash Table, Two Pointers, Sorting

---

## Análisis de Complejidad

Donde `n = nums1.length` y `m = nums2.length`:

| Dimensión   | Notación       | Justificación                                                                                                                                                                                                                                                                                                                                                                                                                                               |
| ----------- | -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n + m)`     | Dos pasadas lineales: la primera recorre el array más pequeño (garantizado por la guarda recursiva) para construir el mapa de frecuencias en `O(n)`; la segunda recorre el array más grande para consultar y decrementar conteos en `O(m)`. Las operaciones sobre Map / array asociativo son `O(1)` amortizado. La llamada recursiva de intercambio, cuando ocurre, no añade trabajo adicional: es una sola invocación de profundidad 1 sin bucles propios. |
| **Espacio** | `O(min(n, m))` | La guarda de tamaño garantiza que `counts` indexe siempre el array **más pequeño**, acotando el mapa a `O(min(n, m))` entradas únicas. El array `result` ocupa en el peor caso `O(min(n, m))` posiciones adicionales. El Call Stack crece en a lo sumo **1 frame de profundidad** por la recursión de intercambio, lo que es efectivamente `O(1)` de stack.                                                                                                 |

---

## Intuición y Enfoque

### Técnica: Frequency Map (Hash Map de Conteo) con Optimización de Tamaño

Este problema es la variante con multiconjunto del problema #349. La diferencia fundamental es que los duplicados **sí cuentan**: si un valor aparece 3 veces en `nums1` y 2 veces en `nums2`, debe aparecer **2 veces** en el resultado, ya que la intersección respeta la multiplicidad mínima de cada elemento.

Un `Set` simple (como en #349) no puede modelar esto: colapsa todas las ocurrencias en una sola entrada. Se necesita una estructura que registre **cuántas veces** aparece cada valor: un **mapa de frecuencias**.

**Fase 0 — Optimización de espacio (guarda de tamaño):**

```
if (nums1.length > nums2.length) return intersect(nums2, nums1)
```

Antes de construir cualquier estructura, se verifica cuál array es más pequeño y se garantiza que `nums1` siempre lo sea. El mapa de frecuencias se construirá sobre `nums1`, acotando su tamaño a `O(min(n, m))` en lugar de `O(max(n, m))`. Es una optimización de espacio con costo cero en tiempo.

**Fase 1 — Indexación de frecuencias:**
Se recorre `nums1` y se construye un mapa `counts` donde cada clave es un valor del array y su valor asociado es el número de veces que aparece. Ej: `[1, 2, 2, 1]` → `{1: 2, 2: 2}`.

**Fase 2 — Intersección con consumo de cuota:**
Se recorre `nums2`. Por cada elemento, si existe en `counts` con un conteo mayor a cero, se agrega al resultado y se **decrementa el conteo en 1**. El decremento modela el consumo de una "unidad de cuota" del elemento: la siguiente aparición del mismo valor en `nums2` solo calificará si aún quedan unidades disponibles en `counts`. Cuando el conteo llega a cero, el elemento ya no pasa el filtro.

**Invariante:** El conteo en `counts[x]` en todo momento representa cuántas ocurrencias adicionales de `x` procedentes de `nums2` pueden aún ser aceptadas en el resultado.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- `new Map()` es la estructura idiomática para el mapa de frecuencias. A diferencia de un objeto literal `{}`, `Map` acepta cualquier tipo como clave (incluyendo objetos y NaN), mantiene el orden de inserción, y expone `size`, `has()`, `get()`, `set()` como API explícita. Para conteo de enteros es más semántico y seguro que un objeto.
- `counts.get(num) || 0` es el patrón de inicialización por defecto: si `get` retorna `undefined` (clave no existente), el operador `||` cortocircuita a `0`, evitando `NaN` en la suma. Es el equivalente funcional del operador `??` de PHP (nullish coalescing), aunque `||` es más amplio: también evalúa como falsy a `0`, `false`, `""`. Para este caso específico es seguro porque los conteos nunca serán `0` en el mapa (un conteo de `0` se deja pero ya no pasa el filtro `count > 0`).
- La guarda de tamaño usa **recursión directa** sobre la propia función `intersect`, intercambiando los argumentos. En JS, las funciones `var`-declaradas son accesibles por su nombre dentro de su propio scope, permitiendo esta auto-referencia sin necesidad de `this`.
- `counts.set(num, count - 1)` actualiza el conteo decrementado, manteniendo la entrada en el Map con valor `0` cuando se agota. Esto es deliberado: la verificación `if (count > 0)` actúa como filtro, haciendo innecesario eliminar la clave.

### PHP

- PHP no tiene una clase `Map` nativa con API equivalente. La solución simula el mapa de frecuencias con un **array asociativo**: `$counts[$num] = ($counts[$num] ?? 0) + 1`. El operador `??` (nullish coalescing, disponible desde PHP 7) es más preciso que `||` en este contexto: solo devuelve `0` si la clave no existe o es `null`, sin cortocircuitar en `0` o `false`, lo que lo hace más seguro para manejo de conteos.
- La guarda de tamaño invoca `$this->intersect($nums2, $nums1)`: a diferencia de JS donde la función no pertenece a una clase, en PHP el método es parte de `Solution` y requiere `$this->` para la auto-referencia. Ambos `count()` se evalúan en la condición, lo que es `O(1)` en PHP ya que los arrays mantienen su longitud internamente en metadatos.
- `isset($counts[$num]) && $counts[$num] > 0` es la condición compuesta en PHP. El `isset` previo evita un `Undefined index` notice antes de comparar el valor con `0`. En JS, `counts.get(num)` retorna `undefined` cuando la clave no existe, y `undefined > 0` evalúa a `false` sin error, por lo que la doble verificación no es necesaria.
- `$counts[$num]--` usa el operador de post-decremento directamente sobre el array asociativo, que PHP resuelve de forma equivalente a `$counts[$num] = $counts[$num] - 1`. Es más conciso que la versión JS, que requiere leer el valor previo con `get` antes de actualizar con `set`.

---

## Lecciones Clave

- **Patrón "Frequency Map / Histogram":** Cada vez que un problema involucre conteo de ocurrencias, multiplicidad de elementos, o validación de anagramas/permutaciones, el mapa de frecuencias es la estructura de referencia. Aparece en detección de anagramas, verificación de permutaciones, problemas de ventana deslizante con restricciones de frecuencia, y en sistemas reales como contadores de rate-limiting, histogramas de métricas, e inventarios de recursos. La clave del patrón es modelar cada elemento como una "cuota consumible" que se decrementa conforme se usa.

- **Optimización "Index the Smaller" — Principio de asimetría deliberada:** La guarda `if (n > m) swap(nums1, nums2)` antes de construir cualquier estructura de datos es un principio de diseño general: cuando se opera con dos colecciones de tamaños distintos, **siempre invertir el rol de las estructuras** para que la de mayor costo (el mapa indexado) sea la más pequeña. Este razonamiento aplica directamente a hash joins en bases de datos (el optimizador de queries elige el lado "build" y el lado "probe" basándose en cardinalidad), a operaciones de merge en sistemas distribuidos, y a cualquier algoritmo donde el espacio de búsqueda pueda elegirse dinámicamente.
