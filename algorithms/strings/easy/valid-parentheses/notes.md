# Valid Parentheses

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud de la cadena `s` -> El bucle recorre la cadena exactamente una vez. En cada iteración, las operaciones sobre el arreglo usado como pila (`push`/`pop` en JS, `[]` y `array_pop()` en PHP) son de tiempo amortizado $O(1)$. La consulta al mapa de correspondencias (`map[char]` / `isset(self::BRACKET_MAP[$char])`) también es $O(1)$. La complejidad total es estrictamente lineal.
- **Espacio**: $O(N)$ -> En el peor caso, la pila (`stack`) almacena hasta $N/2$ elementos: cuando la cadena está formada completamente por paréntesis de apertura (por ejemplo, `"((((("`). La pila crece en proporción al número de aperturas pendientes de cierre. Los mapas de correspondencias (`map` / `BRACKET_MAP`) tienen exactamente **3 entradas fijas** y representan un coste constante $O(1)$ adicional.

## Intuición y Enfoque

El problema solicita verificar si una cadena de paréntesis, corchetes y llaves está correctamente balanceada: cada símbolo de cierre debe corresponderse con el símbolo de apertura **más reciente** aún no cerrado.

La propiedad LIFO (_Last In, First Out_) de este problema es su rasgo definitorio: el último paréntesis de apertura visto es siempre el primero que debe cerrarse. Esta estructura de anidamiento jerárquico es precisamente la que define el comportamiento de una **Pila (Stack)**, convirtiéndola en la estructura de datos canónica para este tipo de problema.

La solución implementa el **Patrón de Validación de Balanceo mediante Pila (Stack-Based Bracket Matching)** con un mapa de correspondencias inverso (cierre → apertura):

1. **Guarda de Longitud Par**: Si la cadena tiene longitud impar, es imposible que esté balanceada (cada apertura requiere exactamente un cierre). Se retorna `false` en $O(1)$ antes de cualquier procesamiento.

2. **Mapa Inverso de Correspondencias**: Se define un mapa donde cada símbolo de **cierre** apunta a su correspondiente símbolo de **apertura** esperado: `')' → '('`, `']' → '['`, `'}' → '{'`. Este diseño inverso permite identificar en una sola consulta si el carácter actual es un cierre y cuál es su apertura correspondiente, sin necesidad de un `else if` adicional para cada tipo.

3. **Ciclo de Procesamiento**:
   - Si `char` es un **cierre** (está en el mapa): se extrae el tope de la pila. Si la pila está vacía, se usa el centinela `"#"` (un carácter que nunca coincidirá con ninguna apertura válida). Si el tope extraído no coincide con la apertura esperada → `return false`.
   - Si `char` es una **apertura** (no está en el mapa): se apila (`push`).

4. **Condición de Éxito**: Al finalizar el recorrido, la cadena es válida si y solo si la pila está vacía (todas las aperturas encontraron su cierre correspondiente).

**El centinela `"#"`** es un detalle de implementación elegante: en lugar de verificar `if (stack.length === 0) return false` por separado antes del `pop`, se unifica la lógica usando un valor ficticio que nunca puede ser un paréntesis de apertura válido, simplificando el flujo de control a una sola comparación.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Objeto Literal como Mapa de Correspondencias**: El mapa `{ ")": "(", "]": "[", "}": "{" }` se define como un objeto literal local dentro de la función. El lookup `map[char]` retorna `undefined` para los símbolos de apertura (que no son claves del objeto), lo que es falsy. La condición `if (map[char])` aprovecha esto para distinguir entre cierres (truthy, tienen un valor en el mapa) y aperturas (falsy, `undefined`).
  - **`Array` como Stack con `push`/`pop`**: JavaScript utiliza su `Array` nativo como pila. Los métodos `.push(char)` y `.pop()` operan sobre el final del arreglo en $O(1)$ amortizado, implementando la semántica LIFO esperada. La condición `stack.length > 0 ? stack.pop() : "#"` es un ternario que evita llamar `.pop()` sobre un arreglo vacío (que retornaría `undefined` en lugar de `"#"`).
  - **Iteración `for...of` sobre la Cadena**: Se usa `for (const char of s)` para iterar directamente sobre los caracteres de la cadena. Este enfoque es más declarativo que el bucle con índice y maneja correctamente caracteres Unicode multi-codepoint (aunque en este problema todos los caracteres son ASCII).
  - **`stack.length === 0` como Condición Final**: El retorno `return stack.length === 0` es una expresión booleana compacta que retorna `true` si la pila está vacía (éxito) y `false` en caso contrario.

- **PHP**:
  - **Constante de Clase `private const BRACKET_MAP`**: A diferencia de JavaScript donde el mapa es una variable local de función, PHP define el mapa como una **constante de clase** (`private const BRACKET_MAP`). Esto tiene implicaciones de diseño importantes: la constante se crea una sola vez en tiempo de compilación (no en cada llamada al método), es compartida por todas las instancias de `Solution`, y es accesible mediante `self::BRACKET_MAP`. Esta es la práctica idiomática en PHP orientado a objetos para valores constantes del algoritmo que no dependen del estado de la instancia.
  - **`isset(self::BRACKET_MAP[$char])` vs `map[char]`**: PHP requiere una verificación explícita con `isset()` para determinar si una clave existe en el arreglo asociativo, ya que el acceso directo `self::BRACKET_MAP[$char]` para una clave inexistente generaría un _Notice_ de PHP (en versiones anteriores a PHP 8) o un _Warning_. `isset()` retorna `true` si la clave existe y su valor no es `null`, sin generar advertencias.
  - **`array_pop()` con `empty()` y Centinela `'#'`**: La lógica `!empty($stack) ? array_pop($stack) : '#'` es equivalente al ternario de JavaScript. `array_pop()` en PHP extrae y retorna el último elemento del arreglo, implementando la semántica LIFO. `empty($stack)` es el equivalente idiomático de `stack.length === 0` en JavaScript.
  - **`$stack[] = $char`**: La sintaxis de appending de PHP (`$stack[] = $char`) es equivalente a `stack.push(char)` en JavaScript. Es la forma idiomática y concisa de PHP para añadir un elemento al final de un arreglo, sin necesidad de llamar a una función explícita de push.
  - **Tipo de Retorno Explícito `bool`**: La firma `public function isValid(string $s): bool` declara tanto el tipo del parámetro como el tipo de retorno con las _type declarations_ de PHP 7+, lo que mejora la seguridad de tipos, la documentación del código y la compatibilidad con herramientas de análisis estático.

## Lecciones Clave

- **La Pila como Estructura Canónica para Anidamiento y Balance**: Este problema es el ejemplo más puro y directo de por qué las pilas existen: cualquier problema que involucre **anidamiento jerárquico** donde el elemento más recientemente abierto debe ser el primero en cerrarse (semántica LIFO) tiene una solución natural con una pila. Este patrón se aplica en parsers de expresiones aritméticas, compiladores (análisis sintáctico), evaluación de expresiones con operadores de precedencia, validación de XML/HTML, deshacer/rehacer en editores, y navegación hacia atrás en historial de páginas.
- **El Mapa Inverso (Cierre → Apertura) como Simplificador de Lógica Condicional**: El diseño de mapear el símbolo de **cierre** a su **apertura esperada** —en lugar del enfoque inverso (apertura → cierre)— unifica la verificación de correspondencia en una única consulta: "¿el tope de la pila es la apertura que este cierre espera?". Si se mapeara apertura → cierre, sería necesario hacer un `peek` (mirar el tope sin extraer), buscar en el mapa, y comparar, requiriendo más operaciones. El mapa inverso permite `pop + compare` en un único paso, eliminando casos especiales. Este diseño de "mapa inverso para verificación inmediata" es aplicable en cualquier problema de correspondencia de pares: etiquetas HTML, operadores balanceados, y estructuras de apertura/cierre en general.
