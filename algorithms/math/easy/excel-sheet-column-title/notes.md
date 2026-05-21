# Excel Sheet Column Title

## Análisis de Complejidad

- **Tiempo**: $O(\log_{26} N)$ donde $N$ es `columnNumber` -> El bucle `while` divide `columnNumber` por 26 en cada iteración (tras el ajuste de decremento). El número de iteraciones es equivalente al número de dígitos en la representación bijective base-26 del número, que es proporcional a $\lfloor \log_{26} N \rfloor + 1$. En la práctica, para el rango de enteros válido de LeetCode (hasta $2^{31} - 1 \approx 2.1 \times 10^9$), el máximo de iteraciones es aproximadamente $\lceil \log_{26}(2^{31}) \rceil = 7$. Dentro de cada iteración, todas las operaciones son de tiempo constante $O(1)$.
- **Espacio**: $O(\log_{26} N)$ -> El espacio requerido es proporcional al número de caracteres del título resultante, que crece logarítmicamente con `columnNumber`. En JavaScript se usa un arreglo `result` de como máximo $\lfloor \log_{26} N \rfloor + 1$ elementos. En PHP, la cadena `$result` crece carácter a carácter mediante prepend. En ambos casos, el espacio adicional de trabajo es logarítmico, no lineal.

## Intuición y Enfoque

El problema es el **inverso exacto** de _Excel Sheet Column Number_: convertir un entero a su título de columna de Excel (`1` → `"A"`, `26` → `"Z"`, `27` → `"AA"`, `702` → `"ZZ"`).

La solución natural sería una conversión estándar a base 26: calcular `columnNumber % 26` para obtener el dígito actual y `columnNumber / 26` para obtener el cociente. Sin embargo, el sistema de columnas de Excel es un **sistema de numeración posicional bijective en base 26** — no existe el dígito `0`. Los dígitos van de `{A=1, B=2, ..., Z=26}` en lugar del convencional `{0, 1, ..., 25}`. Esto significa que `columnNumber % 26 === 0` no representa la letra `'\0'` sino la letra `'Z'`, y el cociente debe ajustarse para reflejar este "préstamo" de valor.

El **ajuste clave** es el decremento `columnNumber--` **antes** de calcular el módulo y el cociente en cada iteración. Esta operación de `-1` transforma el sistema bijective de rango `[1, 26]` al sistema estándar de rango `[0, 25]`, permitiendo que la división y el módulo operen correctamente con las propiedades habituales de la aritmética modular:

| `columnNumber` | Tras `--` | `% 26` | Letra | Nuevo `columnNumber` (÷ 26) |
| -------------- | --------- | ------ | ----- | --------------------------- |
| 27 (`"AA"`)    | 26        | 0      | A     | 1                           |
| 1 (÷26)        | 0         | 0      | A     | 0 (termina)                 |
| 702 (`"ZZ"`)   | 701       | 25     | Z     | 26                          |
| 26 (÷26)       | 25        | 25     | Z     | 0 (termina)                 |

Los dígitos se generan **del menos significativo al más significativo** (de derecha a izquierda), por lo que al finalizar el bucle deben invertirse para producir la representación correcta.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **`String.fromCharCode(65 + remainder)`**: Se utiliza el método estático nativo `String.fromCharCode()` para convertir un punto de código Unicode a su carácter correspondiente. Dado que `'A'` tiene código ASCII 65, la expresión `65 + remainder` mapea el rango `[0, 25]` al rango de caracteres `['A', 'Z']`. La constante `65` está codificada directamente en lugar de computarse con `'A'.charCodeAt(0)`, lo cual es igualmente idiomático para una constante fija y bien conocida.
  - **Acumulación en Arreglo + Reversión Final**: Los caracteres se acumulan del LSB al MSB con `.push()` en el arreglo `result`, y al final se revierten con `.reverse()` y se unen en cadena con `.join("")`. Esta estrategia es más eficiente que la prepend de cadenas en cada iteración (que crearía objetos de cadena inmutables intermedios en el heap).
  - **`Math.floor(columnNumber / 26)`**: Se usa la función `Math.floor()` para obtener la división entera del cociente ajustado. En JavaScript, el operador `/` siempre retorna un número de punto flotante, por lo que el truncamiento es obligatorio para evitar que `columnNumber` se convierta en un valor decimal.

- **PHP**:
  - **`chr(65 + $remainder)`**: PHP utiliza la función nativa `chr()` para convertir un valor ASCII a su carácter correspondiente, equivalente semántico de `String.fromCharCode()` en JavaScript. La expresión `chr(65 + $remainder)` produce el mismo mapeo `[0, 25]` → `['A', 'Z']`.
  - **Prepend de Cadena en Lugar de Arreglo + Reverse**: La diferencia más significativa entre ambas implementaciones es la estrategia de construcción del resultado. PHP construye la cadena directamente mediante **prepend** (`$result = chr(...) . $result`), anteponiendo el nuevo carácter al inicio de la cadena acumulada. Esto produce la cadena en el orden correcto sin necesidad de inversión al final. El coste de cada prepend es $O(K)$ donde $K$ es la longitud actual de `$result`, pero dado que el resultado tiene como máximo ~7 caracteres, este coste es efectivamente constante.
  - **`intdiv($columnNumber, 26)`**: PHP provee la función nativa `intdiv()` (disponible desde PHP 7.0) para realizar división entera directa sin necesidad de cast o `floor()`. Esta es la forma idiomática y semánticamente clara en PHP moderno para división entera de enteros positivos, evitando la confusión que podría generar el operador `/` al retornar `float` en ciertos contextos.

## Lecciones Clave

- **Ajuste de Offset como Solución al Sistema Sin Cero (Bijective Numeration)**: El decremento `columnNumber--` antes del módulo es la pieza crítica que diferencia la conversión bijective de la conversión estándar. Siempre que un sistema de numeración posicional comience desde `1` en lugar de `0` (lo que incluye sistemas de identificación de filas/columnas, codificaciones personalizadas, o numeraciones 1-indexed en general), el ajuste de `-1` antes de operar con módulo y división es el patrón canónico para forzar la compatibilidad con la aritmética modular estándar.
- **Generación Inversa con Reconstrucción**: Este problema refuerza el patrón de generar dígitos o caracteres del extremo menos significativo al más significativo (usando módulo sucesivo) y reconstruir el resultado en orden correcto al finalizar. Este mismo patrón es la base de la conversión de cualquier entero a una representación en base arbitraria: binario, hexadecimal, octal, o cualquier codificación alfanumérica personalizada. La elección entre acumulación en arreglo + reversa (JS) o prepend directo a cadena (PHP) depende de las características de eficiencia de manipulación de cadenas de cada lenguaje.
