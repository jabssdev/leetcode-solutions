# Max Consecutive Ones — Technical Notes

> **LeetCode #485** · Difficulty: Easy · Topic: Array

---

## Análisis de Complejidad

| Dimensión   | Notación | Justificación                                                                                                                                                                                                                                                                                 |
| ----------- | -------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Tiempo**  | `O(n)`   | Un único bucle recorre los `n` elementos del array exactamente una vez. Por cada elemento se ejecutan a lo sumo 2 operaciones `O(1)`: un incremento o reset de `currentCount`, y una comparación condicional para actualizar `maxCount`. No existe ningún bucle anidado ni llamada recursiva. |
| **Espacio** | `O(1)`   | Solo se utilizan dos variables escalares enteras: `currentCount` y `maxCount`. No se crean arrays auxiliares, Maps, Sets ni estructuras en heap. El espacio ocupado es constante e independiente del tamaño del input.                                                                        |

---

## Intuición y Enfoque

### Técnica: Single-Pass Scan con Running Maximum (Contadores Duales)

La fuerza bruta identificaría cada posición de inicio de una secuencia de unos y luego usaría un bucle anidado para medir su longitud: `O(n²)` en el peor caso. Otra variante recorrería el array múltiples veces. Ambas son ineficientes.

La solución óptima se basa en la observación de que **el estado relevante del algoritmo en cualquier punto del recorrido puede reducirse a dos valores escalares**:

- `currentCount`: longitud de la racha de unos que se está extendiendo en este momento.
- `maxCount`: longitud máxima de racha observada en todo el recorrido hasta ahora.

**Mecanismo de operación por cada elemento:**

- Si `nums[i] === 1`: la racha activa se extiende → `currentCount++`. Si la racha activa supera el máximo histórico, el máximo se actualiza inmediatamente → `maxCount = currentCount`.
- Si `nums[i] !== 1` (es `0`): la racha activa se rompe → `currentCount = 0`. El `maxCount` no se toca: ya absorbió el máximo de la racha que acaba de terminar gracias a las actualizaciones inline.

**Invariante del algoritmo:** En todo momento, `maxCount` contiene la longitud máxima de cualquier racha de unos **completamente procesada o en curso**. Al finalizar el recorrido, `maxCount` contiene la respuesta global.

**¿Por qué `maxCount` no necesita actualizarse en el `else`?** Cuando la racha se rompe con un `0`, el valor de `currentCount` justo antes del reset ya fue comparado con `maxCount` en la última iteración donde era `1`. El reset a `0` no genera un nuevo máximo candidato. La actualización inline dentro del `if` captura el máximo de cada racha en el exacto momento en que cada uno de sus elementos es procesado, haciendo innecesario cualquier flush al terminar cada racha.

---

## Notas Políglotas (JavaScript vs PHP)

### JavaScript

- El bucle usa `for` con índice explícito: `for (let i = 0; i < nums.length; i++)`. Aunque el índice `i` no se usa dentro del cuerpo (solo `nums[i]`), es el patrón estándar en JS cuando se trabaja con arrays indexados. Una alternativa idiomática sería `for (const num of nums)`, que accede directamente al valor sin gestionar el índice, pero ambas son equivalentes en rendimiento para este caso.
- La actualización del máximo se realiza mediante un `if` explícito: `if (currentCount > maxCount) { maxCount = currentCount; }`. Una alternativa expresiva sería `maxCount = Math.max(maxCount, currentCount)`, que es semánticamente equivalente. La versión con `if` evita el overhead de la llamada a función `Math.max` (mínimo, pero presente), y es más explícita sobre la condición de actualización. Ambos enfoques son igualmente válidos; la elección es de estilo.
- La comparación `nums[i] === 1` usa igualdad estricta. En un array binario `[0, 1]` de JS, donde todos los elementos son del tipo `number`, `==` sería funcionalmente equivalente, pero `===` es la práctica defensiva correcta: elimina cualquier posibilidad de coerción con `true`, `"1"`, o `1.0`.
- `let` se usa para ambas variables mutables (`maxCount`, `currentCount`), lo que comunica su intención de ser modificadas durante el recorrido, a diferencia de `const`.

### PHP

- El bucle usa `foreach ($nums as $num)` en lugar de un `for` indexado. `foreach` itera directamente sobre los valores del array sin gestionar un índice, siendo el idioma más limpio y expresivo de PHP para recorridos secuenciales cuando el índice no es necesario. Es la elección canónica en PHP para este patrón, en contraste con el `for` indexado de JS.
- La actualización del máximo también usa un `if` explícito: `if ($currentCount > $maxCount) { $maxCount = $currentCount; }`. PHP tiene `max($a, $b)` como función nativa equivalente, pero como en JS, la versión con `if` es más eficiente al evitar la overhead de invocación de función y es igualmente legible.
- La comparación `$num === 1` usa strict comparison de PHP. Para `Integer[]` (declarado en el docblock), el tipo ya está garantizado como `int`, por lo que `==` sería funcionalmente segura. Sin embargo, `===` es la práctica correcta en PHP para prevenir comparaciones laxas inesperadas con `true` (que es igual a `1` bajo `==`), `"1"`, o `1.0`.
- `$currentCount++` utiliza el operador de post-incremento de PHP, funcionalmente idéntico al `currentCount++` de JS. Ambos modifican la variable in-place en `O(1)`.

---

## Lecciones Clave

- **Patrón "Running Maximum con Reset" (Streak Tracking):** Este ejercicio es la implementación más pura del patrón de seguimiento de rachas: un contador activo que crece con cada elemento que cumple una condición y se reinicia a cero cuando la condición falla, junto con un acumulador de máximo que captura el pico histórico. Memorizar este patrón de dos variables (`current` + `best`) permite resolver inmediatamente problemas como: máxima racha de ganancias consecutivas, uptime máximo de un servicio, ventana más larga de señal continua en series temporales, y cualquier problema de "longest run" sobre secuencias binarias o condicionadas.

- **Actualización inline vs. flush al terminar racha:** La decisión de actualizar `maxCount` **dentro** del `if (num === 1)` en lugar de **en el `else`** (o después del bucle) es un patrón de diseño sutil pero importante: actualizar el acumulador en cada extensión de la racha, no solo al terminarla. Esto hace el algoritmo **streaming-compatible**: puede procesar un flujo infinito de datos y en cualquier momento `maxCount` contiene la respuesta correcta hasta ese instante, sin necesidad de señalizar "fin de racha". Este principio es fundamental en procesamiento de eventos en tiempo real, pipelines de datos y algoritmos sobre streams donde el input no tiene un terminador definido.
