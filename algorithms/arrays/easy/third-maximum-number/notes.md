# Third Maximum Number — Technical Notes

> **LeetCode #414** · Difficulty: Easy · Topic: Array, Sorting

---

## Análisis de Complejidad

| Dimensión   | Notación | Justificación                                                                                                                                                                                                                                                        |
| ----------- | -------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n)`   | Un único bucle recorre los `n` elementos del array exactamente una vez. Por cada elemento, se ejecutan a lo sumo 3 comparaciones de igualdad (deduplicación) y 3 comparaciones de orden (cascada de máximos), todas `O(1)`. No existe bucle anidado ni ordenamiento. |
| **Espacio** | `O(1)`   | Se utilizan exclusivamente 3 variables escalares (`max1`, `max2`, `max3`) independientemente del tamaño del input. No se crean arrays, Maps ni estructuras adicionales en heap.                                                                                      |

> La alternativa naïve de ordenar el array y tomar el tercer elemento único costaría `O(n log n)` en tiempo y `O(1)` o `O(n)` en espacio según el algoritmo de sort. Esta solución es estrictamente superior al lograr `O(n)` sin ningún overhead de ordenamiento.

---

## Intuición y Enfoque

### Técnica: K-Top Elements con Variables Escalares (Cascada de Máximos)

El problema requiere encontrar el **tercer máximo distinto** (sin contar duplicados), con fallback al máximo absoluto si no existen al menos 3 valores únicos.

La solución mantiene en memoria únicamente los **3 mayores valores distintos vistos hasta el momento** usando tres variables escalares (`max1 ≥ max2 ≥ max3`), actualizadas en cascada con cada elemento del array.

**Mecanismo de operación por cada elemento `num`:**

1. **Deduplicación:** Si `num` es igual a cualquiera de los tres máximos actuales, se descarta con `continue`. Esta guarda garantiza que los tres slots representen siempre valores **estrictamente distintos**, cumpliendo la restricción del problema.

2. **Cascada de actualización (insertion shift):** Se evalúan los slots en orden descendente de prioridad:
   - Si `num > max1`: se produce un desplazamiento completo → `max3 ← max2`, `max2 ← max1`, `max1 ← num`.
   - Si `num > max2`: desplazamiento parcial → `max3 ← max2`, `max2 ← num`.
   - Si `num > max3`: actualización simple → `max3 ← num`.

   Este mecanismo de "shift down" es análogo a insertar un elemento en una lista ordenada de tamaño fijo, descartando el menor cuando se supera la capacidad.

3. **Retorno con fallback:** Al finalizar el recorrido, si `max3` sigue siendo el valor centinela inicial (indica que no se encontraron 3 valores distintos), se retorna `max1` (el máximo absoluto). En caso contrario, se retorna `max3`.

**¿Por qué es óptimo?** El algoritmo toma una única decisión por elemento con un número fijo de comparaciones. No requiere ordenamiento, estructuras auxiliares, ni múltiples pasadas sobre el array. Es la implementación mínima viable de un **min-heap de tamaño 3** sin el overhead de una heap real.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- Las variables `max1`, `max2`, `max3` se inicializan a `-Infinity`, la representación del **infinito negativo** del tipo `number` de JS (IEEE 754). Este valor es menor que cualquier número entero representable, lo que permite que la primera comparación `num > max1` sea verdadera para cualquier elemento válido del array, sin necesidad de guardas `=== null` adicionales.
- El uso de `-Infinity` como centinela es semánticamente elegante: el código de actualización de los tres slots es uniforme y no requiere ramas adicionales para el caso "aún no inicializado". Las comparaciones aritméticas funcionan naturalmente.
- La detección de "tercer máximo no encontrado" en el retorno `max3 === -Infinity ? max1 : max3` es directa: si `max3` no fue desplazado de su valor inicial, no existen 3 distintos. Sin embargo, esta condición tiene un **edge case teórico**: si el array contiene el valor `Number.NEGATIVE_INFINITY` como elemento, la comparación fallaría silenciosamente. En el contexto del problema (enteros en el rango `[-2³¹, 2³¹ - 1]`), esto no es un problema práctico, pero es un riesgo a tener en cuenta al generalizar el patrón.
- La comparación de deduplicación usa `===` (igualdad estricta), lo que evita coerciones de tipo involuntarias. Especialmente importante en JS dado su sistema de tipos débil.

### PHP

- Las variables se inicializan a `null` en lugar de `-Infinity`. PHP no tiene una constante de infinito negativo nativa para enteros (aunque existe `INF` para floats). `null` es el idioma PHP para representar "ausencia de valor", lo que semánticamente es más explícito: un slot `null` significa "aún no se ha visto ningún valor para esta posición".
- Como consecuencia, las condiciones de actualización requieren **guardas de nulidad explícitas**: `$max1 === null || $num > $max1`. Sin la guarda `=== null`, comparar un entero contra `null` con `>` en PHP retorna `true` (PHP coerciona `null` a `0` en comparaciones numéricas), lo que causaría bugs si el array contiene solo valores negativos menores que `0`. La guarda explícita hace el código más verboso pero elimina cualquier ambigüedad de coerción.
- La verificación de fallback `$max3 !== null ? $max3 : $max1` es simétrica: `null` como centinela es un indicador más semánticamente limpio de "tercer slot jamás actualizado" que `-Infinity`, y no presenta el edge case que `Number.NEGATIVE_INFINITY` introduce en JS.
- La comparación de deduplicación también usa `===` (strict comparison en PHP), que verifica tipo y valor simultáneamente. `null === $max1` solo sería `true` si `$num` fuera `null`, lo cual no ocurre dado el tipo `Integer[]` del input, haciendo la deduplicación segura.

---

## Lecciones Clave

- **Patrón "K-Top con Variables Escalares":** Cuando `K` es pequeño y fijo (K = 2, 3, 5...), mantener los K mejores elementos en variables escalares individuales o en un array de tamaño K es más eficiente que ordenar el input o usar un heap completo. El mecanismo de cascada ("insertion shift") garantiza `O(1)` por elemento con `K` comparaciones fijas. Este patrón es directamente aplicable a leaderboards de tamaño fijo, selección de los N mejores candidatos en streaming de datos, o rankings parciales en sistemas de recomendación donde `K` está acotado por diseño.

- **Centinela `-Infinity` vs `null` — Elección del valor de "estado no inicializado":** La elección del centinela inicial no es trivial. `-Infinity` unifica el path de código eliminando ramas de nulidad, pero introduce un edge case cuando el dominio del input incluye ese valor. `null` es más semánticamente explícito y seguro pero aumenta la complejidad ciclomática con guardas adicionales. En diseño de sistemas, esta tensión aparece en caches (valor centinela vs flag booleano de "presente"), en bases de datos (NULL vs valor mágico como `-1` o `MIN_INT`), y en APIs (absent field vs campo con valor por defecto). La elección debe documentarse explícitamente en cualquier codebase de producción.
