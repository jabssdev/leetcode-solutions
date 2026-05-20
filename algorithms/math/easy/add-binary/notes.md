# Add Binary

## Análisis de Complejidad

- **Tiempo**: $O(\max(m, n))$ donde $m$ y $n$ son las longitudes de las cadenas `a` y `b` respectivamente -> El bucle `while` itera exactamente $\max(m, n)$ veces procesando los dígitos de ambas cadenas, más como máximo **una iteración adicional** si existe un acarreo final (`carry > 0`) tras agotar ambas cadenas (por ejemplo, `"1" + "1"` produce acarreo en la posición $m+1$). La condición compuesta `i >= 0 || j >= 0 || carry > 0` garantiza que el bucle continúa hasta procesar absolutamente todo el estado. Dentro de cada iteración, todas las operaciones son de tiempo constante $O(1)$. Al finalizar, la inversión del arreglo y la unión de cadena son $O(\max(m, n))$, lo que no altera la complejidad total.
- **Espacio**: $O(\max(m, n))$ -> Se construye un arreglo `result` que almacena los dígitos binarios del resultado en orden inverso. En el caso más largo, el resultado tiene como máximo $\max(m, n) + 1$ dígitos (el bit de acarreo adicional). La cadena de retorno también ocupa este espacio. No se crean estructuras de datos adicionales más allá del arreglo de resultado, por lo que el espacio es lineal respecto al tamaño de la entrada más larga.

## Intuición y Enfoque

El problema solicita sumar dos cadenas binarias de longitud arbitraria y retornar el resultado como una nueva cadena binaria. Un enfoque naïve sería convertir ambas cadenas a enteros, sumarlos, y reconvertir el resultado a binario. Sin embargo, este enfoque fracasa para entradas de gran longitud que excedan los límites de `Number.MAX_SAFE_INTEGER` en JavaScript ($2^{53} - 1$) o `PHP_INT_MAX` en PHP ($2^{63} - 1$), ya que las cadenas de entrada pueden ser arbitrariamente largas.

La solución implementa la **Aritmética de Suma Binaria Dígito a Dígito (Digit-by-Digit Binary Addition)** con **Propagación de Acarreo (Carry Propagation)**, que es exactamente cómo funciona la suma binaria en hardware digital:

1. Se inicializan dos punteros `i` y `j` apuntando a los dígitos menos significativos (extremos derechos) de cada cadena, y `carry = 0`.
2. En cada iteración, se extrae el dígito binario actual de cada cadena (o `0` si el índice ya se agotó), se suman junto con el acarreo de la iteración anterior: `sum = digitA + digitB + carry`.
3. El dígito resultante para la posición actual es `sum % 2` (el bit menos significativo de la suma).
4. El acarreo para la siguiente posición es `Math.floor(sum / 2)` (el bit más significativo de la suma; solo puede ser `0` o `1` en aritmética binaria).
5. Los dígitos se acumulan en un arreglo `result` en **orden inverso** (del menos al más significativo), que se invierte al final antes de retornar.

Este enfoque maneja naturalmente cadenas de longitudes desiguales (usando `0` como relleno implícito para la cadena más corta) y el acarreo final sin necesidad de padding previo ni lógica adicional.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Conversión de Carácter a Entero con Resta de String**: La expresión `a[i] - "0"` aprovecha la coerción implícita de JavaScript: al restar una cadena de otra, JS convierte ambas operandas a número. Dado que `a[i]` es el carácter `"0"` o `"1"`, y el código ASCII de `"0"` es 48, la resta `"1" - "0"` produce `1` y `"0" - "0"` produce `0`. Esta es una forma concisa y idiomática de parsear dígitos individuales en JavaScript sin llamar a `parseInt()`.
  - **Acumulación en Arreglo + `reverse().join("")`**: Los dígitos se acumulan de LSB a MSB con `.push()`, y al final se invierten con `.reverse()` y se concatenan con `.join("")`. Usar un arreglo como buffer de construcción es más eficiente que la concatenación de cadenas en cada iteración, ya que evita la creación de objetos de cadena intermedios inmutables en cada paso.
  - **`Math.floor(sum / 2)`**: Se usa la función nativa `Math.floor()` para obtener el acarreo (la división entera de `sum` entre `2`). En aritmética binaria, `sum` puede valer `0`, `1`, `2`, o `3`, por lo que el acarreo es `0` o `1`.

- **PHP**:
  - **Conversión de Carácter a Entero con Cast Explícito `(int)`**: PHP requiere un cast explícito `(int)$a[$i]` para convertir el carácter `'0'` o `'1'` al entero correspondiente. El acceso a cadenas por índice (`$a[$i]`) retorna una cadena de un carácter en PHP, y el cast `(int)` la convierte directamente a su representación numérica (`0` o `1`). Esta es la forma idiomática y segura en PHP frente a la coerción implícita de JavaScript.
  - **División Entera con `(int)($sum / 2)`**: PHP no tiene un operador de división entera nativo en este contexto (el operador `intdiv()` existe pero es menos común que el cast). La expresión `(int)($sum / 2)` realiza la división aritmética y trunca el resultado con el cast `(int)`, equivalente semántico del `Math.floor()` de JavaScript para valores positivos.
  - **`implode("", array_reverse($result))`**: El equivalente PHP de `result.reverse().join("")` es la composición de `array_reverse()` (que crea una copia invertida del arreglo) e `implode("", ...)` (que concatena los elementos con el separador dado, análogo a `.join("")`). A diferencia de `array_reverse()` in-place no disponible de forma directa con la sintaxis de `implode`, la copia adicional es el enfoque idiomático en PHP.

## Lecciones Clave

- **Aritmética de Precisión Arbitraria Operando sobre Cadenas**: Este problema es un modelo canónico de cómo implementar operaciones aritméticas sobre números de magnitud arbitraria sin depender de los tipos numéricos nativos del lenguaje. Siempre que los operandos excedan los límites de los enteros nativos, operar carácter a carácter sobre su representación en cadena —con propagación de acarreo— es la técnica fundamental de la aritmética de Big Integer. El mismo patrón se aplica en _Add Strings_, _Multiply Strings_, y cualquier sistema que requiera precisión decimal o binaria ilimitada.
- **Construcción Inversa con Buffer de Arreglo**: El patrón de acumular resultados en orden inverso (del LSB al MSB) en un arreglo y revertirlo al final es preferible a la inserción al inicio de la cadena (que en algunos lenguajes es $O(N)$ por operación). Construir desde el extremo menos significativo y revertir al terminar es una técnica de eficiencia en la construcción de resultados numéricos o secuenciales que se aplica en suma de números, generación de representaciones en bases alternativas, y cualquier proceso de construcción de secuencias donde el orden natural de producción es inverso al orden de salida.
