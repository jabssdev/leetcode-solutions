# Missing Number — Technical Notes

> **LeetCode #268** · Difficulty: Easy · Topic: Array, Math, Bit Manipulation

---

## Análisis de Complejidad

### Solución JavaScript — Gauss Sum

| Dimensión   | Notación | Justificación                                                                                                                                        |
| ----------- | -------- | ---------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n)`   | Un único bucle `for` recorre los `n` elementos del array para acumular `actualSum`. La fórmula de Gauss se evalúa en `O(1)`.                         |
| **Espacio** | `O(1)`   | Solo se utilizan variables escalares primitivas (`n`, `expectedSum`, `actualSum`). No se crean estructuras de datos auxiliares (Maps, Sets, arrays). |

### Solución PHP — XOR Bitwise

| Dimensión   | Notación | Justificación                                                                                                                         |
| ----------- | -------- | ------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n)`   | Un único bucle `for` aplica la operación XOR sobre cada índice `i` y cada valor `nums[i]` simultáneamente en `O(1)` por iteración.    |
| **Espacio** | `O(1)`   | La acumulación se realiza en la variable escalar `$missing`, inicializada con `$n`. No se aloja ninguna estructura adicional en heap. |

> Ambas soluciones son **óptimas en tiempo y espacio** para este problema.

---

## Intuición y Enfoque

Este problema admite múltiples soluciones correctas, pero las dos implementadas representan los enfoques más elegantes desde la teoría algorítmica.

### Enfoque 1 — Suma de Gauss (JavaScript)

Se apoya en la identidad matemática de la sumatoria de los primeros `n` enteros naturales:

$$\text{expectedSum} = \frac{n(n+1)}{2}$$

La idea central es que si el array `[0, 1, ..., n]` tuviera todos sus elementos, su suma sería exactamente `expectedSum`. Al restar la suma real `actualSum` del array de entrada, el resultado es precisamente el número que falta, ya que es el único que no contribuyó a la suma.

- **Técnica:** Reducción matemática (suma aritmética de Gauss).
- **Por qué es superior a fuerza bruta:** Evita el uso de un Set/Map para marcar vistos (`O(n)` espacio). Una sola pasada de suma y una resta es suficiente.

### Enfoque 2 — XOR Bitwise (PHP)

Se basa en dos propiedades fundamentales del operador XOR (`^`):

1. `x ^ x = 0` — Todo número XOR consigo mismo es cero.
2. `x ^ 0 = x` — Todo número XOR cero es el mismo número.

Al inicializar `$missing = n` y luego aplicar `$missing ^= i ^ nums[i]` en cada iteración, se están cancelando por pares todos los índices con sus valores correspondientes. El único elemento que no tiene pareja —el número faltante— permanece sin cancelarse en `$missing`.

- **Técnica:** Bit Manipulation (XOR cancellation / pairing).
- **Por qué es superior a fuerza bruta:** Opera directamente a nivel de bits, sin aritmética de enteros grandes ni riesgo de overflow en plataformas con enteros de 32 bits.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- Se utilizan variables `const` y `let` con **block scope** (ES6+), lo que refuerza la inmutabilidad de `n` y `expectedSum` como constantes de cálculo.
- El acceso a `nums[i]` en el bucle es equivalente a iterar con `nums.reduce()`, pero el `for` explícito evita el overhead de la función de callback y hace la intención más declarativa para este contexto.
- La expresión `(n * (n + 1)) / 2` opera sobre el tipo `number` de JS (IEEE 754 float de 64 bits). Para `n` hasta `10⁴` (límite del problema), no hay riesgo de pérdida de precisión.

### PHP

- La variable `$missing` se inicializa con `$n` (equivalente a empezar el XOR con el índice superior del rango completo `[0..n]`), lo que elimina la necesidad de un bucle adicional de `0` a `n`.
- PHP tipifica `$nums` como `Integer[]`, garantizando que la operación `^=` opere sobre enteros nativos sin coerciones de tipo implícitas.
- `count($nums)` es la función idiomática de PHP para obtener la longitud de un array indexado, equivalente funcional de `.length` en JS. Ambas son operaciones `O(1)`.
- El operador `^=` es un operador de asignación compuesto en PHP, funcionalmente idéntico al de JavaScript: `$a ^= $b` equivale a `$a = $a ^ $b`.

---

## Lecciones Clave

- **Patrón "Complement / Cancellation":** Tanto la Suma de Gauss como el XOR son instancias del mismo patrón conceptual: construir una representación del estado "completo o ideal" y derivar el elemento faltante como el residuo. Memorizar este patrón permite resolver de forma óptima problemas de _single missing element_, _single duplicate_, o _finding the odd one out_ con `O(n)` tiempo y `O(1)` espacio sin estructuras auxiliares.

- **Escalabilidad sin overhead de memoria:** Cuando un problema requiere detectar anomalías en una secuencia numérica conocida, siempre evaluar primero si existe una propiedad matemática (aritmética, geométrica) o una propiedad bit a bit (XOR, AND) que permita colapsar toda la información en una sola variable escalar, antes de recurrir a Hash Sets o arrays de marcado que consumen `O(n)` espacio adicional.
