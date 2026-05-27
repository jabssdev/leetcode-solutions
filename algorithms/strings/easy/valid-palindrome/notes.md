# Valid Palindrome

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud de la cadena `s` -> Los dos punteros `left` y `right` avanzan exclusivamente hacia el centro: `left` solo se incrementa y `right` solo se decrementa. Cada carácter de la cadena es visitado **como máximo una vez** por cualquiera de los dos punteros. Aunque en cada iteración del bucle `while` solo uno de los dos punteros puede avanzar (o ambos si hay un par alfanumérico válido), el total de pasos acumulados está estrictamente acotado por $N$. La verificación `isAlphanumeric` / `ctype_alnum` y la comparación insensible a mayúsculas son ambas $O(1)$.
- **Espacio**: $O(1)$ -> La solución opera directamente sobre la cadena original sin crear versiones filtradas o normalizadas. Solo se mantienen dos variables escalares de puntero (`left` y `right`). No se crean cadenas auxiliares, arreglos de caracteres alfanuméricos, ni estructuras de datos adicionales. La función `isAlphanumeric` en JavaScript tampoco crea estructuras de datos; opera únicamente con el código ASCII del carácter.

## Intuición y Enfoque

El problema solicita verificar si una cadena, considerando únicamente los caracteres alfanuméricos e ignorando mayúsculas/minúsculas, es un palíndromo.

Un enfoque naïve filtraría primero la cadena (eliminando no-alfanuméricos y normalizando a minúsculas), crearía una nueva cadena limpia, y luego la compararía con su reversa. Esto es $O(N)$ en tiempo pero $O(N)$ en espacio adicional por las cadenas intermedias creadas.

La solución implementa la técnica de **Dos Punteros (Two Pointers) con Filtrado en Línea (In-Line Filtering)**, que elimina la necesidad de preprocesar la cadena:

1. Se inicializan `left = 0` (inicio de la cadena) y `right = s.length - 1` (fin de la cadena).
2. El bucle `while (left < right)` continúa mientras los punteros no se hayan cruzado o solapado (condición de terminación para palíndromos).
3. En cada iteración, el algoritmo implementa una **lógica de avance selectivo con tres ramas**:
   - Si `s[left]` **no** es alfanumérico → ignorar el carácter, avanzar `left++`.
   - Si `s[right]` **no** es alfanumérico → ignorar el carácter, retroceder `right--`.
   - Si ambos son alfanuméricos → comparar en minúsculas. Si difieren → `return false`. Si son iguales → avanzar ambos punteros (`left++`, `right--`).
4. Si el bucle termina sin retornar `false`, la cadena es un palíndromo → `return true`.

La **prioridad de evaluación** de las tres ramas es crítica: primero se saltan los no-alfanuméricos de izquierda, luego de derecha, y solo cuando ambos extremos son alfanuméricos se realiza la comparación. Esto garantiza que nunca se comparen caracteres con basura de puntuación o espacios.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Función `isAlphanumeric` con Comparación de Rangos ASCII**: En lugar de usar una expresión regular (que tendría overhead de compilación y ejecución), se implementa una función auxiliar que verifica si el código ASCII del carácter cae en uno de los tres rangos válidos: `[48, 57]` (dígitos `0-9`), `[65, 90]` (letras mayúsculas `A-Z`), `[97, 122]` (letras minúsculas `a-z`). Esta verificación de rangos enteros es significativamente más rápida que una regex para una comprobación tan simple y fija.
  - **`String.prototype.charCodeAt(0)`**: Se usa dentro de `isAlphanumeric` para obtener el punto de código Unicode del carácter. Para los 128 caracteres ASCII, esto es equivalente al código ASCII estándar.
  - **`String.prototype.toLowerCase()`**: La normalización a minúsculas se realiza al momento de la comparación (`s[left].toLowerCase() !== s[right].toLowerCase()`), evitando la creación de una copia completa normalizada de toda la cadena. Solo se crean dos strings temporales de longitud 1 por comparación.
  - **Arrow Function para `isAlphanumeric`**: La función de clasificación se define como una `const` arrow function en el ámbito del módulo, no como función anidada dentro de `isPalindrome`. Esto la hace reutilizable y evita su recreación en cada llamada a `isPalindrome`.

- **PHP**:
  - **`ctype_alnum()`**: PHP provee la función nativa `ctype_alnum()` del módulo `ctype` (Character Type) para verificar si un string contiene únicamente caracteres alfanuméricos. Internamente implementa la misma verificación de rangos ASCII que la función `isAlphanumeric` de JavaScript, pero como una función nativa de C compilada, tiene un rendimiento superior y elimina la necesidad de implementar la lógica manualmente. `ctype_alnum($s[$left])` para un string de un carácter es el equivalente directo de `isAlphanumeric(s[left])`.
  - **`strtolower()`**: PHP usa la función nativa `strtolower()` para la normalización a minúsculas al momento de la comparación, equivalente a `.toLowerCase()` de JavaScript. Para strings de un solo carácter ASCII (el caso de este problema), `strtolower()` es $O(1)$ y no tiene overhead de multi-byte como `mb_strtolower()`.
  - **`elseif` vs `else if`**: PHP usa la construcción `elseif` (una sola palabra) en lugar del `else if` (dos palabras) de JavaScript. Aunque PHP acepta ambas formas, `elseif` es la forma idiomática preferida por la comunidad PHP ya que es una construcción léxica única y no una combinación de `else` + `if`. En términos de comportamiento, son absolutamente equivalentes.

## Lecciones Clave

- **Two Pointers con Filtrado In-Line como Alternativa $O(1)$ al Preprocesamiento**: Este ejercicio es el caso de estudio canónico del patrón Two Pointers para verificación de palíndromos en cadenas con ruido (caracteres no relevantes). La técnica de filtrar los caracteres inválidos directamente dentro del bucle de dos punteros —avanzando el puntero correspondiente sin comparar— elimina la necesidad de crear una cadena limpia intermedia ($O(N)$ de espacio) y demuestra que el filtrado y la comparación pueden fusionarse en un único recorrido. Este patrón se recicla en _Valid Palindrome II_ (eliminar un carácter) y en cualquier verificación de simetría sobre secuencias con elementos a ignorar.
- **Verificación de Rangos ASCII vs. Regex para Clasificación de Caracteres**: La elección de `charCodeAt(0)` con comparaciones de rangos enteros en lugar de una regex como `/[a-z0-9]/i` es una micro-optimización válida en contextos de alta frecuencia. Para clasificaciones de caracteres simples sobre un conjunto fijo y conocido (alfanumérico, vocal, consonante, hexadecimal), la verificación de rangos es más rápida, más legible en términos de intención y portable. La alternativa nativa `ctype_alnum()` de PHP demuestra que los lenguajes maduros suelen ofrecer estas funciones de clasificación como primitivas del lenguaje, evitando la necesidad de implementarlas manualmente.
