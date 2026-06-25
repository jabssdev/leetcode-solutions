# Move Zeroes — Technical Notes

> **LeetCode #283** · Difficulty: Easy · Topic: Array, Two Pointers

---

## Análisis de Complejidad

| Dimensión   | Notación | Justificación                                                                                                                                                                                                    |
| ----------- | -------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n)`   | Un único bucle `for` recorre linealmente los `n` elementos del array. Cada elemento es visitado exactamente una vez; la operación de swap en la condición interna es `O(1)`. No existe bucle anidado.            |
| **Espacio** | `O(1)`   | La modificación se realiza **in-place** sobre el array original. Las únicas variables auxiliares son los escalares `insertPos`, `i` y `temp`. No se crean estructuras de datos adicionales (Maps, Sets, arrays). |

---

## Intuición y Enfoque

### Técnica: Two Pointers — Slow / Fast Pointer (Write / Read)

Se utilizan dos punteros lógicos que avanzan a ritmos distintos sobre el mismo array:

- **`insertPos` (slow pointer / write pointer):** Señala siempre la próxima posición disponible donde debe colocarse un elemento no cero. Avanza únicamente cuando se procesa un elemento válido.
- **`i` (fast pointer / read pointer):** Escanea el array completo de izquierda a derecha en búsqueda de elementos no cero.

**Mecanismo de operación:**

Cuando el fast pointer `i` encuentra un elemento distinto de cero, se compara su posición con `insertPos`. Si difieren (`i !== insertPos`), significa que hay un cero en `insertPos` que debe ser desplazado; se realiza un **swap** entre ambas posiciones. Si son iguales, el elemento ya está en su lugar correcto y no se requiere operación. En ambos casos, `insertPos` avanza.

La **invariante del algoritmo** garantiza que al finalizar el recorrido:

1. Todos los elementos no cero han sido compactados hacia el lado izquierdo en su orden relativo original.
2. Todos los ceros han quedado desplazados hacia el lado derecho, ya que ocuparon las posiciones que el write pointer fue dejando atrás.

**Ventaja sobre fuerza bruta:** Un enfoque naïve crearía un array auxiliar con los no-ceros y luego rellenaría el resto con ceros (`O(n)` espacio). Esta solución logra el mismo resultado sin memoria adicional, realizando swaps condicionales en una sola pasada. La guarda `if (i !== insertPos)` es una optimización crítica: evita swaps innecesarios cuando el puntero lento no ha encontrado aún ningún cero, evitando escrituras redundantes en memoria.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- El array `nums` se recibe como referencia implícita (los objetos y arrays en JS se pasan **by reference** por defecto), por lo que la modificación in-place sobre el parámetro se refleja automáticamente en el array original del llamador, sin necesidad de declaración explícita.
- El swap manual con variable `temp` es idiomático en este contexto. JS moderno permite swap con **destructuring assignment** (`[nums[insertPos], nums[i]] = [nums[i], nums[insertPos]]`), pero la versión con `temp` es más explícita y eficiente al evitar la creación de un array temporal.
- La comparación estricta `!== 0` (strict inequality) es la elección correcta en JS: descarta `null`, `false`, `""` u otros valores _falsy_ que no son el número `0`, evitando bugs sutiles de coerción de tipos que afectarían a `== 0`.

### PHP

- La firma `function moveZeroes(&$nums)` utiliza **paso por referencia explícito** (`&`). En PHP, los arrays se copian por valor por defecto (copy-on-write), por lo que sin el `&` la función operaría sobre una copia y el array original no se modificaría. Esta es la diferencia semántica más importante entre ambas implementaciones.
- El uso de `count($nums)` pre-calculado en `$n` antes del bucle es una micro-optimización válida en PHP: evita que el intérprete evalúe `count()` en cada iteración del `for`, aunque en versiones modernas de PHP el compilador JIT puede optimizar esto internamente.
- La comparación `!== 0` es igualmente estricta en PHP, operando sobre el tipo `Integer` declarado en el docblock, lo que garantiza consistencia de tipos y evita comparaciones laxas con valores falsy del lenguaje (`false`, `""`, `null`).
- El swap con `$temp` es el patrón canónico en PHP para intercambio de valores en arrays, ya que PHP no tiene una sintaxis de destructuring de arrays para swap tan concisa como JS en contextos de asignación directa.

---

## Lecciones Clave

- **Patrón "Read / Write Two Pointers":** Este ejercicio es la forma canónica del patrón de dos punteros donde un puntero _lee_ y otro _escribe_. Es directamente aplicable a problemas de partición in-place: remover duplicados de un array ordenado, filtrar elementos según una condición, separar pares de impares, o la fase de partición del algoritmo **QuickSort**. Siempre que el objetivo sea **compactar** un array bajo una condición sin usar memoria extra, este patrón es el punto de partida.

- **La guarda de igualdad como optimización de escritura:** El condicional `if (i !== insertPos)` antes del swap ilustra un principio general de ingeniería: **evitar operaciones de escritura idempotentes**. Las escrituras en memoria (especialmente en cache lines compartidas o buffers de I/O) son costosas. Validar si una operación es realmente necesaria antes de ejecutarla es una práctica que escala desde algoritmos en arrays hasta sistemas de bases de datos (dirty flags, copy-on-write, lazy evaluation).
