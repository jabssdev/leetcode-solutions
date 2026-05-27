# Length of Last Word

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud de la cadena `s` -> En el peor caso, ambas soluciones recorren toda la cadena de derecha a izquierda: por ejemplo, si la cadena consiste en una única palabra sin espacios finales. En el caso promedio, la iteración termina anticipadamente al encontrar el espacio que delimita el inicio de la última palabra. Cada iteración realiza exclusivamente comparaciones de carácter en tiempo constante $O(1)$. La solución PHP realiza dos pasadas separadas (una para espacios finales, otra para la palabra), pero ambas siguen siendo $O(N)$ en total con la misma constante.
- **Espacio**: $O(1)$ -> Ambas soluciones utilizan únicamente variables escalares (`length`, `i`) sin crear cadenas auxiliares, arreglos de tokens, ni estructuras de datos adicionales de tamaño variable. No se realiza ninguna división o tokenización de la cadena original.

## Intuición y Enfoque

El problema solicita retornar la longitud de la última palabra de una cadena `s` que puede contener espacios finales y múltiples palabras separadas por espacios.

El enfoque naïve dividiría la cadena por espacios (`split(" ")` / `explode()`), filtraría tokens vacíos, y retornaría la longitud del último token. Aunque correcto, implica $O(N)$ de espacio adicional para el arreglo de tokens y el procesamiento innecesario de todas las palabras cuando solo nos interesa la última.

La solución óptima es un **Recorrido de Derecha a Izquierda con Detección de Límites de Palabra**, que localiza directamente la última palabra sin asignaciones adicionales:

**JavaScript — Un Único Bucle con Máquina de Estados Implícita de Dos Fases**:
El bucle `for` desde el final de la cadena implementa una máquina de estados de dos fases dentro de un único bucle:

1. **Fase 1 — Saltar espacios finales**: mientras `s[i] === " "` y `length === 0`, el bucle simplemente decrementa `i` (la condición `else if (length > 0)` no se cumple ya que `length` es `0`).
2. **Fase 2 — Contar la última palabra**: en cuanto se encuentra el primer carácter no espacio, `length` comienza a incrementarse. Cuando se encuentra un espacio después de haber contado al menos un carácter (`else if (length > 0)`), la última palabra ha terminado y se retorna `length` inmediatamente.
3. Si el bucle termina sin encontrar un espacio delimitador (la cadena tiene una sola palabra sin espacios finales), `length` se retorna al finalizar el `for`.

**PHP — Dos Bucles Explícitos con Fases Separadas**:
PHP separa explícitamente las dos fases en dos bucles `while` consecutivos:

1. **Bucle 1**: avanza `$i` hacia la izquierda mientras `$s[$i] === ' '` (descarta espacios finales).
2. **Bucle 2**: avanza `$i` hacia la izquierda mientras `$s[$i] !== ' '` contando los caracteres con `$length++`.
3. Retorna `$length` tras el segundo bucle.

Ambas estrategias son algorítmicamente equivalentes en complejidad; la diferencia radica en que JS funde las dos fases en un único bucle con una condición compuesta, mientras PHP las separa en dos bucles con condiciones simples, priorizando la claridad sobre la compacidad.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Acceso a Carácter por Índice con `s[i]`**: JavaScript permite acceder a caracteres de una cadena directamente mediante `s[i]`, retornando un string de longitud 1 (no un entero de código ASCII como en PHP). La comparación `s[i] !== " "` compara cadenas directamente, lo cual es semánticamente más legible que comparar códigos de caracteres.
  - **Máquina de Estados en Un Solo Bucle**: El flujo `if (no espacio) → incrementar` / `else if (longitud > 0) → retornar` integra la detección del espaciado final y el conteo de la palabra en una sola pasada. La condición `else if (length > 0)` es la pieza que distingue entre "espacio antes de la última palabra" (ignorado, `length === 0`) y "espacio tras la última palabra" (señal de fin, `length > 0`). Si se usara `else` sin la condición `length > 0`, la función retornaría `0` prematuramente al encontrar el primer espacio final.
  - **Retorno Fuera del Bucle**: Si la cadena es una sola palabra sin espacios finales (por ejemplo, `"hello"`), el bucle `for` termina al llegar a `i = -1` sin retornar dentro del bucle. El `return length` final cubre este caso.

- **PHP**:
  - **Dos Bucles `while` con Fases Explícitas**: PHP separa la lógica en dos bucles con condiciones simples y claras. Este estilo es preferible cuando la legibilidad y mantenibilidad tienen prioridad sobre la compacidad del código, ya que cada bucle tiene una responsabilidad semántica única y bien definida.
  - **Inicialización Explícita de `$i`**: `$i = strlen($s) - 1` se calcula una sola vez antes de los bucles, evitando la evaluación de `strlen()` en cada iteración. Esta es una práctica de optimización idiomática en PHP donde la evaluación de funciones de cadena en condiciones de bucle puede tener coste no trivial en versiones antiguas del motor.
  - **Sin Retorno Anticipado**: A diferencia de la solución JS que usa `return` dentro del bucle, la solución PHP retorna una única vez al final de la función. Este estilo de "single exit point" es una práctica de legibilidad preferida en muchos equipos de desarrollo, ya que hace el flujo de control más predecible y el código más fácil de depurar con breakpoints.

## Lecciones Clave

- **Recorrido Inverso como Técnica de Localización del Extremo Relevante**: Cuando el dato de interés se encuentra al **final** de la estructura de datos (la última palabra, el último elemento no nulo, el último pico, etc.), recorrer de derecha a izquierda con salida temprana es la técnica óptima. Evita procesar toda la estructura desde el inicio cuando solo importa el sufijo, y es directamente aplicable en _Last Stone Weight_, _Find Last Index_, búsqueda del último elemento que satisface una condición, y procesamiento de logs donde el evento más reciente es el más relevante.
- **Máquina de Estados de Fases vs. Bucles Separados como Trade-off de Legibilidad**: Este problema ilustra con claridad el trade-off de diseño entre condensar la lógica multi-fase en un único bucle (JS) o separarla en múltiples bucles especializados (PHP). El primer enfoque es más compacto y potencialmente más eficiente en overhead de bucle, pero requiere que el lector decodifique la máquina de estados implícita. El segundo enfoque tiene mayor legibilidad y mantenibilidad. La elección debe basarse en las convenciones del equipo y la complejidad de las fases involucradas.
