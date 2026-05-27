# Find the Index of the First Occurrence in a String

## Análisis de Complejidad

- **Tiempo**: $O((H - N) \cdot N)$ donde $H$ es la longitud de `haystack` y $N$ la de `needle` -> Ambas soluciones implementan el algoritmo de **Búsqueda de Subcadenas por Ventana Deslizante Naïve (Sliding Window Naive)**. El bucle exterior recorre $H - N + 1$ posiciones de inicio posibles. Por cada posición, se realiza una comparación de subcadena de longitud $N$: en JavaScript mediante `substring()` que internamente compara hasta $N$ caracteres, y en PHP mediante el bucle interior que itera hasta $N$ posiciones con salida temprana (`break`). En el peor caso (por ejemplo, `haystack = "aaaaa..."` y `needle = "aaab"`), todas las posiciones requieren comparación completa, produciendo $O((H - N) \cdot N)$. En el caso promedio con alfabetos naturales, la salida temprana del primer carácter no coincidente reduce significativamente las comparaciones reales.
- **Espacio**: $O(N)$ para JavaScript y $O(1)$ para PHP -> En JavaScript, `haystack.substring(i, i + nLen)` crea una **nueva cadena** de longitud $N$ en el heap en cada iteración, ya que las cadenas de JS son inmutables. Esto implica un uso de espacio auxiliar de $O(N)$ por iteración (aunque el objeto es elegible para garbage collection inmediatamente). En PHP, la comparación se realiza carácter a carácter directamente sobre la cadena original mediante acceso por índice, sin asignación de subcadenas, resultando en $O(1)$ de espacio auxiliar puro.

## Intuición y Enfoque

El problema es la búsqueda de una subcadena (`needle`) dentro de otra cadena (`haystack`), retornando el índice de la primera ocurrencia o `-1` si no existe. Es el problema canónico de **String Matching** (*Pattern Matching*) en Ciencias de la Computación.

Ambas soluciones implementan el **Algoritmo de Búsqueda Naïve con Ventana Deslizante** —la solución de fuerza bruta que es también la base conceptual de algoritmos más avanzados como KMP, Boyer-Moore y Rabin-Karp— sin embargo, difieren en su implementación interna de la comparación, lo que genera diferencias en asignación de memoria:

**JavaScript — Comparación por Extracción de Subcadena**:
- El bucle exterior desliza una ventana de tamaño `nLen` sobre `haystack`.
- En cada posición `i`, extrae la subcadena `haystack.substring(i, i + nLen)` y la compara directamente con `needle` usando el operador `===`.
- La comparación `===` entre strings en JavaScript realiza una comparación lexicográfica carácter a carácter internamente, con cortocircuito en el primer carácter no coincidente.
- El límite del bucle `i < hLen - nLen + 1` garantiza que solo se visitan posiciones donde una coincidencia completa es posible.

**PHP — Comparación por Bucle Carácter a Carácter con Salida Temprana**:
- El bucle exterior recorre las posiciones de inicio con el límite inclusivo `i <= hLen - nLen`.
- Un bucle interior explícito compara cada carácter `$haystack[$i + $j]` contra `$needle[$j]`.
- Si hay un desacuerdo, `break` sale del bucle interior inmediatamente (salida temprana óptima sin asignación de subcadenas).
- Si el bucle interior llega a `$j === $nLen - 1` sin haber ejecutado `break`, significa que todos los caracteres coincidieron y se retorna `$i`.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **`String.prototype.substring(start, end)`**: Extrae la subcadena del índice `start` (inclusivo) al índice `end` (exclusivo) como una nueva cadena inmutable. A diferencia de `slice()`, `substring()` normaliza índices negativos a `0` y permuta automáticamente `start` y `end` si `start > end`. Para este problema, los índices siempre son no negativos y válidos por la condición del bucle, por lo que ambos métodos serían equivalentes.
  - **`===` para Comparación de Strings**: La comparación estricta entre la subcadena extraída y `needle` es tanto de tipo como de valor, garantizando que solo cadenas idénticas retornen `true`. JavaScript compara strings internamente de forma lexicográfica con cortocircuito, resultando en $O(N)$ en el peor caso por cada comparación.
  - **Límite `hLen - nLen + 1`**: La condición `i < hLen - nLen + 1` (equivalente a `i <= hLen - nLen`) garantiza que la ventana nunca excede el límite de `haystack`. Si `needle` es más larga que `haystack` (`nLen > hLen`), la condición hace que el bucle no ejecute ninguna iteración y la función retorna `-1` directamente, sin requerir una guarda explícita.

- **PHP**:
  - **Guarda Explícita `$nLen === 0`**: PHP incluye una verificación inicial para el `needle` vacío, retornando `0` inmediatamente. Esto es necesario porque con `$nLen = 0`, el límite del bucle exterior sería `$i <= $hLen - 0 = $hLen`, y el bucle interior (`$j < 0`) no ejecutaría ninguna iteración, haciendo que la condición `$j === $nLen - 1` (es decir, `$j === -1`) nunca se alcance, produciendo un retorno incorrecto de `-1`. La versión en JavaScript maneja esto implícitamente: `substring(i, i + 0)` retorna `""`, y `"" === ""` (si `needle` es `""`) sería `true` en la primera iteración, retornando `0` correctamente.
  - **Acceso a Carácter por Índice (`$haystack[$i + $j]`)**: PHP permite acceder a caracteres individuales de una cadena mediante la sintaxis de corchete, similar a los arreglos. Internamente, `$haystack[$i + $j]` retorna el byte en esa posición sin crear una nueva cadena en el heap, lo que explica el $O(1)$ de espacio de la solución PHP frente a la solución JS.
  - **Bucle Interior con `break` y Retorno en el Último Carácter**: La condición `if ($j === $nLen - 1) return $i;` al final del bucle interior verifica si el índice actual es el último de `needle`, lo que significa que todos los caracteres previos han coincidido (no se ejecutó `break`). Este patrón es equivalente a comparar toda la subcadena pero sin materializar la subcadena en memoria.

## Lecciones Clave

- **El Algoritmo Naïve como Fundamento del String Matching**: La búsqueda de subcadenas por ventana deslizante $O(H \cdot N)$ es el punto de partida conceptual obligatorio antes de abordar algoritmos avanzados como **KMP** ($O(H + N)$ con tabla de fallos), **Boyer-Moore** (sublineal en el caso promedio con heurísticas de salto), o **Rabin-Karp** ($O(H + N)$ esperado con hashing de ventana deslizante). Dominar el naïve es esencial para entender qué optimización introduce cada algoritmo avanzado y cuándo vale la pena la complejidad de implementación adicional.
- **Materialización de Subcadenas vs. Comparación In-Place como Trade-off de Memoria/Legibilidad**: La diferencia entre la solución JS (que extrae una subcadena con `substring`) y la PHP (que compara carácter a carácter) ilustra un trade-off frecuente en ingeniería de software: **legibilidad y concisión del código** (JS, una línea de comparación) vs. **eficiencia de memoria** (PHP, $O(1)$ de espacio sin asignaciones intermedias). En sistemas de alta frecuencia, procesamiento de texto a escala, o entornos con memoria limitada, evitar la materialización de subcadenas temporales puede ser una optimización significativa.
