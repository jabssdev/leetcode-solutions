# Find All Numbers Disappeared in an Array — Technical Notes

> **LeetCode #448** · Difficulty: Easy · Topic: Array, Hash Table

---

## Análisis de Complejidad

| Dimensión   | Notación | Justificación                                                                                                                                                                                                                                                                                             |
| ----------- | -------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n)`   | Dos pasadas lineales e independientes sobre el array de tamaño `n`. El primer bucle marca presencias (`O(n)`); el segundo recolecta ausencias (`O(n)`). Cada operación interna (`Math.abs` / `abs`, negación, comparación) es `O(1)`. Total: `O(2n)` = `O(n)`.                                            |
| **Espacio** | `O(1)`   | No se crean estructuras de datos auxiliares (Maps, Sets, arrays de marcado). Las marcas de presencia se almacenan **in-place** en el propio array de entrada, usando el signo del valor como bit de estado. El array `result` de salida no se contabiliza como espacio extra por convención del problema. |

> Esta solución alcanza la complejidad óptima teórica para el problema: es imposible mejorar el tiempo por debajo de `O(n)` (todo elemento debe ser examinado al menos una vez), y `O(1)` de espacio extra es el mínimo absoluto posible.

---

## Intuición y Enfoque

### Técnica: Index as Hash — Negative Marking (Firma de Signo como Bit de Presencia)

La fuerza bruta construiría un `Set` o array booleano de tamaño `n` para registrar qué números del rango `[1, n]` fueron vistos, luego recorrería el rango buscando ausencias: `O(n)` tiempo y `O(n)` espacio extra. Es correcta pero subóptima en memoria.

La solución óptima explota una propiedad única del enunciado: el array contiene valores en el rango `[1, n]` y tiene exactamente `n` posiciones indexadas desde `0` hasta `n-1`. Existe por tanto una **biyección natural** entre los valores del array y los índices del mismo:

$$\text{valor } v \in [1, n] \longleftrightarrow \text{índice } (v - 1) \in [0, n-1]$$

Esta biyección permite usar **el propio array como tabla hash**, donde el índice `v - 1` actúa como clave y el **signo del valor almacenado** en esa posición actúa como bit de presencia (`positivo = no visto`, `negativo = visto`).

**Fase 1 — Marcado (Presencia):**

Por cada elemento `nums[i]`, se calcula el índice objetivo `idx = |nums[i]| - 1` (el `abs` es crítico: si el valor ya fue negado en una iteración anterior, el `abs` recupera el valor original para calcular el índice correcto). Si `nums[idx] > 0`, se niega: `nums[idx] = -nums[idx]`. Si ya es negativo, no se toca (evita doble negación que restauraría el valor original).

Al terminar esta fase, la posición `i` tiene un valor **negativo** si y solo si el número `i + 1` **aparece al menos una vez** en el array original.

**Fase 2 — Recolección (Ausencia):**

Se recorre el array. Cualquier posición `i` donde `nums[i] > 0` indica que el número `i + 1` **nunca fue marcado** — es decir, **nunca apareció** en el input. Ese índice se convierte en resultado: `result.push(i + 1)`.

**Invariante central:** El signo del elemento en la posición `i` codifica la presencia o ausencia del número `i + 1` en el conjunto original, sin usar ningún byte adicional de memoria más allá del array ya existente.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- `Math.abs(nums[i])` recupera el valor absoluto del elemento actual antes de calcular el índice. `Math.abs` opera sobre el tipo `number` (IEEE 754), acepta enteros negativos y retorna su valor positivo en `O(1)`. Es la función estándar para valor absoluto en JS; no existe un operador unario equivalente.
- La condición `if (nums[index] > 0)` antes de negar evita la doble negación: si el mismo valor aparece duplicado en el array, el segundo encuentro encontraría `nums[index]` ya negativo y lo dejaría intacto, preservando correctamente la marca de presencia.
- La negación `nums[index] = -nums[index]` modifica el array **in-place** directamente. En JS, los arrays son objetos pasados por referencia, por lo que esta mutación sobre el parámetro `nums` es válida y no requiere declaración especial, a diferencia de PHP.
- `result.push(i + 1)` acumula los números desaparecidos. El `+ 1` convierte del índice base-0 al valor base-1 del rango del problema.

### PHP

- `abs($nums[$i])` es la función equivalente de PHP para valor absoluto de enteros. A diferencia de JS, PHP tiene una función `abs()` que maneja nativamente tanto enteros como floats, retornando el mismo tipo que recibe. Para `Integer[]`, el retorno es siempre `int`.
- La función no declara `&$nums` (paso por referencia) en su firma, lo que significa que PHP opera sobre una **copia local** del array de entrada. Las modificaciones de negación `$nums[$index] = -$nums[$index]` afectan únicamente a esa copia, lo cual es correcto porque el array mutado solo se usa como estructura temporal interna — el resultado se construye en `$result` y se retorna. Esto contrasta con `moveZeroes` (#283) donde el pass-by-reference era esencial.
- La condición `if ($nums[$index] > 0)` antes de negar es idéntica semánticamente a la versión JS. En PHP, la comparación `> 0` sobre un `int` es estrictamente aritmética sin coerciones de tipo implícitas, dado que el docblock garantiza `Integer[]`.
- `$result[] = $i + 1` es el append dinámico idiomático de PHP. El array `$result` se construye secuencialmente con los índices base-0 convertidos a valores base-1.
- `count($nums)` se pre-calcula en `$n` antes del primer bucle, evitando llamadas repetidas en la condición del `for`. Se reutiliza este mismo `$n` en ambos bucles, lo que es correcto ya que `count` no cambia al negar valores (solo los signos se alteran, no la longitud).

---

## Lecciones Clave

- **Patrón "Index as Hash / Negative Marking":** Cuando el dominio de los valores de un array es exactamente el rango `[1, n]` (o `[0, n-1]`) y el array tiene `n` posiciones, el propio array puede actuar como tabla hash sin costo adicional de memoria. El signo del elemento en cada posición codifica un bit de estado (presente/ausente, visitado/no visitado). Este patrón es la base de algoritmos como la detección de duplicados in-place, el reordenamiento cíclico (cyclic sort), y la búsqueda de todos los duplicados en un array (#442). Es aplicable siempre que se cumplan dos condiciones: los valores están acotados por el tamaño del array, y el dominio del problema tolera la mutación temporal del input.

- **Valor absoluto como "decodificador de estado":** El `Math.abs()` / `abs()` aplicado antes de usar un valor como índice es el mecanismo que hace al algoritmo **reentrante**: permite procesar un elemento cuyo valor ya fue negado por una iteración anterior, recuperando el índice original sin necesidad de restaurar el valor. Este patrón de "leer a través del estado codificado" aparece en técnicas avanzadas como la codificación de grafos en matrices de adyacencia in-place y en algoritmos de compresión que reutilizan buffers de entrada. La clave es que la operación de decodificación (`abs`) sea inversa a la de codificación (negación) y no destruya la información original.
