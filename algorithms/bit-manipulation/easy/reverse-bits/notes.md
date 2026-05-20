# Reverse Bits

## Análisis de Complejidad

- **Tiempo**: $O(1)$ -> El bucle `for` se ejecuta exactamente **32 iteraciones** fijas, una por cada bit de un entero sin signo de 32 bits. Este número es una constante determinada por el ancho del tipo de dato, no por la magnitud del valor de entrada. Dentro de cada iteración, las tres operaciones bitwise (`<<=`, `|=`, `>>=`) se ejecutan en un solo ciclo de reloj del procesador cada una. Por lo tanto, la complejidad temporal es constante.
- **Espacio**: $O(1)$ -> El algoritmo utiliza únicamente dos variables escalares: `result` (acumulador del número invertido) e `i` (contador del bucle). No se crean arreglos, cadenas de bits, pilas ni ninguna otra estructura de datos auxiliar, garantizando un consumo de memoria constante.

## Intuición y Enfoque

El problema solicita invertir el orden de los 32 bits de un entero sin signo. Por ejemplo, la representación binaria `00000010100101000001111010011100` debe transformarse en `00111001011110000010100101000000`.

Un enfoque ingenuo sería convertir el número a una cadena binaria, invertir la cadena y reconvertirla a entero. Esto introduciría costes innecesarios de asignación de memoria para la cadena, parsing y conversión, además de ser propenso a errores con el padding de ceros a la izquierda.

La solución implementa una técnica de **Manipulación de Bits (Bitwise) con Extracción y Construcción Bit a Bit**, conceptualmente análoga a invertir un arreglo leyendo desde un extremo y escribiendo en el otro:

1. Inicializamos `result = 0`, que será el contenedor donde construiremos el número invertido bit a bit.
2. En cada una de las 32 iteraciones, realizamos tres microoperaciones atómicas:
   - **`result <<= 1`** — Desplazamos `result` un bit a la izquierda, abriendo espacio en la posición menos significativa (LSB) para recibir el siguiente bit.
   - **`result |= n & 1`** — Extraemos el bit menos significativo de `n` mediante la máscara `n & 1`, y lo insertamos en la posición LSB de `result` mediante OR bit a bit. Esta operación "copia" el bit de la cola de `n` a la cola de `result`.
   - **`n >>= 1`** — Desplazamos `n` un bit a la derecha, descartando el bit que acabamos de extraer y exponiendo el siguiente bit para la próxima iteración.
3. Tras 32 iteraciones, todos los bits de `n` han sido extraídos de derecha a izquierda e insertados en `result` de izquierda a derecha, logrando la inversión completa.

El mecanismo es esencialmente una **cola de desensamblaje y una cola de ensamblaje** operando simultáneamente: `n` se desensambla bit a bit desde su LSB mientras `result` se ensambla bit a bit hacia su MSB.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Operador de Desplazamiento a la Derecha Sin Signo (`>>>`)**: La línea final `return result >>> 0` es el detalle técnico más crítico y sutil de esta solución. En JavaScript, todas las operaciones bitwise (`<<`, `>>`, `|`, `&`) convierten internamente los operandos a **enteros con signo de 32 bits (Int32)**. Esto significa que si el bit 31 (MSB) del resultado es `1`, JavaScript interpreta el número como negativo en complemento a dos. El operador `>>> 0` (_unsigned right shift_ de 0 posiciones) fuerza la reinterpretación del patrón de bits como un **entero sin signo de 32 bits (Uint32)**, convirtiendo un resultado negativo erróneo en el valor positivo correcto esperado por LeetCode. Sin este operador, entradas donde el bit invertido más significativo sea `1` producirían respuestas negativas incorrectas.
  - **Desplazamiento a la Derecha con Signo (`>>=`)**: Se utiliza `n >>= 1` (desplazamiento aritmético) para consumir los bits de `n`. Para este problema, usar `>>` o `>>>` es equivalente al desplazar `n` hacia la derecha, ya que el bit de signo propagado por `>>` nunca se lee (el bucle se ejecuta exactamente 32 veces y la máscara `n & 1` solo examina el LSB).

- **PHP**:
  - **Enteros Nativos de 64 Bits**: En plataformas de 64 bits, PHP utiliza enteros de 64 bits (`zend_long`). Para un entero sin signo de 32 bits, los 32 bits superiores son ceros, por lo que las operaciones bitwise producen resultados correctos sin necesidad de conversiones especiales de signo.
  - **Sin Necesidad de `>>> 0`**: A diferencia de JavaScript, PHP **no** requiere una reinterpretación de signo al final (`return $result` es suficiente). Esto se debe a que PHP opera con enteros de 64 bits y el resultado de invertir 32 bits nunca alcanza el bit de signo del entero de 64 bits (bit 63), por lo que el valor siempre se interpreta correctamente como positivo.
  - **Operadores Bitwise Idénticos**: Los operadores `<<=`, `|=`, `&` y `>>=` en PHP tienen la misma semántica que en JavaScript para este rango de valores. La estructura del bucle y las tres microoperaciones por iteración son idénticas, demostrando la alta portabilidad de la lógica bitwise entre ambos lenguajes.

## Lecciones Clave

- **Extracción y Construcción Bit a Bit como Patrón de Transformación**: La técnica de desensamblar un número bit a bit desde un extremo mientras se ensambla otro desde el extremo opuesto es un patrón fundamental en manipulación de bits. Se aplica directamente en conversión de endianness (little-endian ↔ big-endian) en protocolos de red, serialización binaria, codificación/decodificación de datos comprimidos, y cualquier escenario de bajo nivel donde el orden de los bits determina la semántica del dato.
- **Conciencia de la Representación de Signo entre Lenguajes**: Este ejercicio expone una diferencia arquitectónica crítica entre JavaScript y PHP. JavaScript fuerza Int32 en operaciones bitwise (requiriendo `>>> 0` para reinterpretar como Uint32), mientras que PHP opera con enteros de 64 bits donde el problema no se manifiesta. Comprender cómo cada lenguaje representa internamente los enteros a nivel de bits es una competencia esencial para ingenieros que trabajan en sistemas multiplataforma, criptografía, o interoperabilidad de protocolos binarios.
