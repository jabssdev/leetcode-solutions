# Sqrt(x)

## Análisis de Complejidad

- **Tiempo**: $O(\log N)$ donde $N$ es el valor de `x` -> El espacio de búsqueda se inicializa en el rango $[1, \lfloor x/2 \rfloor]$ y se reduce a la mitad en cada iteración del bucle mediante la eliminación de una de las dos mitades (izquierda o derecha). El número máximo de iteraciones es $\lfloor \log_2(\lfloor x/2 \rfloor) \rfloor + 1 \approx \log_2 N - 1$. Para el límite máximo de LeetCode ($x = 2^{31} - 1$), esto resulta en como máximo ~30 iteraciones.
- **Espacio**: $O(1)$ -> El algoritmo mantiene únicamente tres variables escalares enteras: `left`, `right` y `ans`. No se crean arreglos, tablas ni ninguna estructura auxiliar de tamaño variable. La búsqueda binaria es inherentemente iterativa en esta implementación, por lo que no hay crecimiento del Call Stack por recursión.

## Intuición y Enfoque

El problema solicita calcular $\lfloor \sqrt{x} \rfloor$ (la raíz cuadrada entera truncada hacia abajo) sin usar funciones de exponenciación o raíz cuadrada nativas. Un enfoque de fuerza bruta probaría todos los enteros desde `1` hasta `x` buscando el mayor `k` tal que `k² ≤ x`, con complejidad $O(\sqrt{N})$ — demasiado lento para entradas de hasta $2^{31}$.

La clave del problema es reconocer que la función $f(k) = k^2$ es **monótonamente creciente** en el dominio de los enteros positivos. Esto convierte la búsqueda del mayor `k` con `k² ≤ x` en un problema canónico de **Búsqueda Binaria (Binary Search) sobre el espacio de respuestas**, reduciendo la complejidad a $O(\log N)$.

**Optimizaciones Críticas de la Implementación**:

1. **Guarda para `x < 2`**: Los casos `x = 0` y `x = 1` son casos base que retornan `x` directamente. Esto evita que `right = Math.floor(x / 2) = 0` (para `x = 1`) produzca un rango de búsqueda vacío o incorrecto.

2. **Límite superior inicial de `x / 2`**: Para cualquier `x ≥ 2`, la raíz cuadrada entera es siempre ≤ `x / 2`. Esto reduce el espacio de búsqueda inicial a la mitad respecto a `[1, x]`, ahorrando una iteración de log adicional.

3. **Cálculo del Punto Medio sin Desbordamiento**: `mid = left + Math.floor((right - left) / 2)` en lugar de `Math.floor((left + right) / 2)`. La suma `left + right` puede desbordarse el rango de `Int32` para valores de `x` cercanos a $2^{31}$. La forma `left + (right - left) / 2` es la formulación **overflow-safe** estándar de la búsqueda binaria.

4. **Comparación `mid <= x / mid` en lugar de `mid * mid <= x`**: La expresión `mid * mid` puede producir desbordamiento de enteros para valores grandes de `mid`. La comparación equivalente `mid <= x / mid` (dividir ambos lados de `mid² ≤ x` entre `mid`) opera con divisiones que nunca desbordan el rango de enteros para los tipos numéricos de ambos lenguajes.

5. **Variable `ans` como Mejor Candidato**: En lugar de retornar inmediatamente al encontrar una coincidencia exacta, `ans` registra el último valor de `mid` que satisfizo la condición `mid <= x / mid`. Esto permite que el bucle continúe refinando hacia el mayor candidato válido, manejando elegantemente tanto raíces exactas como no exactas en el mismo flujo de control.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **`Math.floor(x / 2)` para el límite superior**: El cálculo del `right` inicial usa `Math.floor()` para garantizar que la división entre `2` produce un entero, ya que el operador `/` de JavaScript siempre retorna `float64`.
  - **`Math.floor((right - left) / 2)` para el punto medio**: Por la misma razón, el cálculo del punto medio requiere `Math.floor()` explícito. La variable `mid` se declara con `const` dentro del bucle, comunicando que es inmutable dentro de cada iteración.
  - **`mid <= x / mid` sin `Math.floor()`**: La comparación `mid <= x / mid` opera correctamente sin truncar `x / mid` a entero en JavaScript. Si `mid` es, por ejemplo, `3` y `x` es `8`, entonces `x / mid ≈ 2.67` y `3 <= 2.67` es `false`, lo que correctamente descarta `mid = 3` para $\lfloor\sqrt{8}\rfloor = 2$. El flotante actúa como un comparador más preciso que la división entera.

- **PHP**:
  - **`intdiv($x, 2)` para el límite superior**: PHP usa la función nativa `intdiv()` (PHP 7+) para obtener el cociente entero de `$x / 2`, produciendo el mismo resultado que `Math.floor(x / 2)` de JavaScript pero de forma más semánticamente explícita y sin el riesgo de obtener un `float`.
  - **`intdiv($right - $left, 2)` para el punto medio**: El cálculo del punto medio usa igualmente `intdiv()`, garantizando que el resultado sea un entero nativo PHP sin necesidad de casts. Esta es la forma idiomática y más legible en PHP moderno para división entera de enteros positivos.
  - **`$mid <= $x / $mid` con aritmética de punto flotante implícita**: Al igual que en JavaScript, PHP evalúa `$x / $mid` como `float` cuando el resultado no es exacto, permitiendo que la comparación sea correcta sin truncamiento explícito. PHP realiza la promoción de tipo de `integer` a `float` automáticamente en divisiones no exactas.
  - **Isomorfismo Algorítmico con Diferencia de División Entera**: La diferencia más sustantiva entre ambas implementaciones es el mecanismo de división entera: `Math.floor(... / 2)` (JS) vs `intdiv(..., 2)` (PHP). La lógica del algoritmo, el manejo de overflow y la estructura de control son completamente isomorfos.

## Lecciones Clave

- **Búsqueda Binaria sobre el Espacio de Respuestas (Binary Search on Answer)**: Este problema es un modelo canónico de una técnica avanzada de búsqueda binaria que no busca en un arreglo, sino en el **dominio de la respuesta** para encontrar el valor límite que satisface una condición monotónica. Siempre que la pregunta sea "¿cuál es el mayor (o menor) valor que satisface la condición $f(k) \leq$ (o $\geq$) objetivo?" y $f$ es monótona, la búsqueda binaria sobre el rango de posibles respuestas es la técnica óptima. Se aplica en _First Bad Version_, _Koko Eating Bananas_, _Capacity to Ship Packages_, y cualquier problema de optimización con función monótona.
- **Invariantes de Seguridad Numérica: Overflow-Safe y División en Lugar de Multiplicación**: Este ejercicio expone dos invariantes críticas de ingeniería en algoritmos numéricos: (1) usar `left + (right - left) / 2` en lugar de `(left + right) / 2` para prevenir desbordamiento al calcular el punto medio, y (2) reformular `k² ≤ x` como `k ≤ x / k` para prevenir desbordamiento al evaluar cuadrados de enteros grandes. Estas dos reescrituras matemáticamente equivalentes son convenciones de seguridad numérica que todo ingeniero debe aplicar reflexivamente en cualquier implementación de búsqueda binaria sobre enteros de gran magnitud.
