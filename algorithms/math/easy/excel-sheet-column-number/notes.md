# Excel Sheet Column Number

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud de la cadena `columnTitle` -> El algoritmo realiza un único recorrido lineal sobre cada carácter de la cadena (`for...of` en JavaScript, `for` con índice en PHP). En cada iteración, se realizan exclusivamente operaciones aritméticas de tiempo constante $O(1)$: obtención del código de carácter, una resta, una multiplicación y una suma. No hay bucles anidados ni recursión.
- **Espacio**: $O(1)$ -> El algoritmo solo mantiene tres variables escalares: `result` (el acumulador numérico), `baseCode` (la constante de offset del carácter `'A'`), y `value` (el valor del carácter actual). No se crean arreglos, mapas, tablas de conversión ni ninguna estructura auxiliar de tamaño variable. El consumo de memoria es constante independientemente de la longitud de la cadena de entrada.

## Intuición y Enfoque

El problema solicita convertir un título de columna en estilo Excel (por ejemplo, `"A"` → `1`, `"Z"` → `26`, `"AA"` → `27`, `"AB"` → `28`) a su número de columna correspondiente.

La observación clave es que el sistema de numeración de columnas de Excel es esencialmente un **sistema de numeración posicional en base 26**, pero con una diferencia crucial respecto al sistema decimal o binario estándar: **no existe el dígito `0`**. En lugar de `{0, 1, ..., 25}`, los dígitos van de `{A=1, B=2, ..., Z=26}`. Esto es lo que se conoce como un sistema numérico posicional _bijective base-26_.

La solución implementa la **Evaluación de Polinomio de Horner (Horner's Method)** adaptada a base 26:

Para una cadena `"BCD"`, el valor es:
$$B \cdot 26^2 + C \cdot 26^1 + D \cdot 26^0 = 2 \cdot 676 + 3 \cdot 26 + 4 = 1382$$

En lugar de calcular potencias explícitas de 26 (que requeriría conocer la longitud de antemano o calcular exponenciaciones), la **Regla de Horner** factoriza el polinomio para evaluarlo de izquierda a derecha en una sola pasada con multiplicaciones y sumas simples:
$$((B \cdot 26 + C) \cdot 26 + D) = ((2 \cdot 26 + 3) \cdot 26 + 4) = (55 \cdot 26 + 4) = 1382$$

La acumulación `result = result * 26 + value` en cada iteración implementa exactamente esta factorización, procesando los dígitos del más significativo al menos significativo, "desplazando" el resultado acumulado a la izquierda en base 26 y sumando el nuevo dígito.

**Detalle del Offset**: La constante `baseCode = charCode('A') - 1` (= 64 en ASCII) se calcula una sola vez fuera del bucle. Restarla del código de cualquier letra produce directamente su valor numérico: `charCode('A') - 64 = 1`, `charCode('Z') - 64 = 26`. El `-1` transforma el rango ASCII `[65, 90]` al rango de valores `[1, 26]`.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **`String.prototype.charCodeAt(0)`**: Se utiliza el método nativo `.charCodeAt(0)` para obtener el punto de código Unicode (código ASCII para letras latinas) de un carácter. La llamada `"A".charCodeAt(0)` retorna `65`, y `"A".charCodeAt(0) - 1` = `64` es la constante base que se reutiliza en cada iteración como `baseCode`.
  - **Precálculo fuera del Bucle**: `baseCode` se declara con `const` fuera del bucle `for...of`. Esto comunica que es una constante fija del algoritmo (no depende de la iteración) y permite al motor V8 optimizarla como un valor constante durante la compilación JIT.
  - **Iteración `for...of` sobre la Cadena**: JavaScript permite iterar directamente sobre los caracteres de una cadena con `for...of`, produciendo un código más declarativo que el acceso por índice. Esta característica de ES6 maneja correctamente caracteres Unicode multi-codepoint (aunque en este problema todas las entradas son ASCII).

- **PHP**:
  - **Función `ord()`**: PHP utiliza la función nativa `ord()` para obtener el valor ASCII de un carácter, equivalente a `.charCodeAt(0)` de JavaScript. La expresión `ord('A') - 1` = `64` produce la misma constante `$baseCode` que en la versión JS.
  - **Acceso a Cadena por Índice (`$columnTitle[$i]`)**: A diferencia de JavaScript donde `for...of` itera directamente sobre caracteres, PHP requiere un bucle `for` clásico con índice numérico y acceso por corchete para obtener cada carácter individual. En PHP, las cadenas son arrays de bytes, por lo que `$columnTitle[$i]` retorna el byte en la posición `$i`, lo cual funciona correctamente para caracteres ASCII.
  - **Precálculo de `strlen($n)`**: Se precalcula `$n = strlen($columnTitle)` antes del bucle. Aunque `strlen()` es $O(1)$ en PHP (la longitud se almacena como metadato en la cadena), esta práctica idiomática evita una llamada de función en cada iteración del `for`.
  - **Isomorfismo Matemático Total**: La expresión central del algoritmo `$result = $result * 26 + $value` es idéntica a la de JavaScript, demostrando que la lógica matemática del método de Horner es completamente portable entre lenguajes.

## Lecciones Clave

- **El Método de Horner como Evaluación Eficiente de Sistemas Posicionales**: La evaluación de `result = result * 26 + digit` es la implementación de la Regla de Horner, que evalúa un polinomio en $O(N)$ sin calcular potencias explícitas. Este mismo patrón se aplica directamente en la conversión de cualquier sistema posicional al decimal: conversión de binario (`base 2`), hexadecimal (`base 16`), octal (`base 8`), y en el problema inverso _Excel Sheet Column Title_. Es la técnica canónica de conversión de bases numéricas en programación.
- **Sistemas Numéricos Posicionales Sin Cero (Bijective Numeration)**: Este ejercicio expone la existencia de sistemas de numeración posicional donde no hay representación del cero, llamados _bijective numeral systems_. En el sistema de columnas de Excel, cada número tiene una única representación alfabética y no existe la "columna 0". Reconocer cuándo un sistema de codificación sigue este esquema (en lugar del estándar con cero) es crucial para no introducir errores de _off-by-one_ al aplicar fórmulas de conversión de bases convencionales.
