# Roman to Integer

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud de la cadena `s` -> El bucle recorre la cadena exactamente una vez de derecha a izquierda. En cada iteración, el acceso al mapa de valores romanos es $O(1)$ (lookup en objeto/arreglo asociativo de tamaño constante con solo 7 entradas), y las operaciones aritméticas de suma/resta son $O(1)$. En la práctica, la longitud máxima de una cadena romana válida es acotada por el valor máximo de 3999 (`MMMCMXCIX`), lo que hace que la complejidad sea efectivamente $O(1)$ para los límites del problema, aunque se expresa como $O(N)$ en el caso general.
- **Espacio**: $O(1)$ -> El mapa de símbolos romanos (`romanMap`) tiene exactamente **7 entradas fijas** independientemente de la entrada. Es una constante del algoritmo, no una función del tamaño de `s`. Las únicas variables adicionales son `total`, `prevValue` y `currentValue`, todas escalares. El espacio total es constante.

## Intuición y Enfoque

El problema solicita convertir un numeral romano (representado como cadena) a su valor entero correspondiente. Los numerales romanos son generalmente aditivos (se suman de izquierda a derecha), pero tienen seis excepciones de **notación sustractiva**: `IV=4`, `IX=9`, `XL=40`, `XC=90`, `CD=400`, `CM=900`.

Un enfoque naïve manejaría explícitamente estos seis casos especiales (por ejemplo, verificando pares de caracteres con `if (s[i] === 'I' && s[i+1] === 'V')`). Aunque funciona, duplica la lógica y requiere manejo adicional del índice `i+1`.

La solución implementa un algoritmo de **Recorrido de Derecha a Izquierda con Regla de Sustracción Implícita**, basado en una propiedad matemática elegante del sistema romano:

> En cualquier numeral romano válido, si un símbolo tiene un valor **menor** que el símbolo a su derecha, ese símbolo debe **restarse** en lugar de sumarse.

Al procesar la cadena **de derecha a izquierda**, el símbolo procesado previamente siempre es el que estaba a la **derecha** del símbolo actual. Por lo tanto:

- Si `currentValue >= prevValue`: el símbolo actual es mayor o igual al de su derecha → es aditivo → `total += currentValue`.
- Si `currentValue < prevValue`: el símbolo actual es menor que el de su derecha → es sustractivo → `total -= currentValue`.

Esta regla unificada maneja automáticamente los seis casos de notación sustractiva sin necesidad de verificar pares explícitamente. La variable `prevValue` (inicializada en `0`) actúa como el "símbolo fantasma a la derecha" para la primera iteración (el último carácter), garantizando que siempre se sume.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Objeto Literal como Hash Map**: El mapa de símbolos romanos se implementa como un objeto literal `{ I: 1, V: 5, ... }`. Las claves del objeto (`I`, `V`, `X`, etc.) son strings implícitamente. El acceso `romanMap[s[i]]` utiliza la notación de corchetes para lookup dinámico por clave string, equivalente a un acceso de hash map con tiempo amortizado $O(1)$. Para este caso de solo 7 claves, un objeto plano es más eficiente en memoria que un `Map` de ES6 ya que evita el overhead de la clase `Map`.
  - **Recorrido `for` con Índice Decreciente**: Se usa `for (let i = s.length - 1; i >= 0; i--)` para iterar de derecha a izquierda. El acceso `s[i]` retorna el carácter como una cadena de longitud 1 en JavaScript, que se usa directamente como clave del objeto `romanMap`.
  - **`const` para `currentValue` dentro del Bucle**: La variable `currentValue` se declara con `const` dentro del bucle, ya que su valor no cambia durante cada iteración. Esto comunica la inmutabilidad intraciclo y permite al motor V8 realizar optimizaciones adicionales.

- **PHP**:
  - **Arreglo Asociativo como Hash Map**: PHP implementa el mapa con un arreglo asociativo `['I' => 1, 'V' => 5, ...]`. Los arreglos asociativos de PHP son implementados internamente como `zend_array` (tablas hash ordenadas), con acceso por clave en $O(1)$ amortizado. Son el equivalente natural de los objetos planos de JavaScript para esta función de lookup.
  - **Acceso a Carácter `$s[$i]`**: Al igual que en JavaScript, PHP permite acceder a caracteres de una cadena por índice con `$s[$i]`, retornando un string de un byte. Para strings ASCII como los numerales romanos, este acceso es completamente correcto y eficiente.
  - **Isomorfismo Algorítmico Total**: La solución PHP es un espejo casi perfecto de la versión JavaScript. La lógica del bucle, la estructura de control `if/else`, y la regla de sustracción son idénticas. Las únicas diferencias son sintácticas: `strlen($s) - 1` vs `s.length - 1` como límite inicial del bucle, `[]` vs `{}` para la declaración del mapa, y las convenciones estándar de PHP (`$`, `->`, etc.).

## Lecciones Clave

- **Regla de Sustracción Implícita mediante Recorrido Inverso**: La transformación de "verificar pares de caracteres con lógica ad-hoc" a "comparar el valor actual con el previo en un recorrido inverso" es un ejemplo de cómo el **cambio de dirección del recorrido** puede eliminar completamente los casos especiales de un algoritmo. Al procesar de derecha a izquierda, lo que era un "lookahead" (`s[i+1]`) se convierte en un simple "lookback" almacenado en `prevValue`, unificando la lógica aditiva y sustractiva en una sola condición. Este principio se aplica en _Integer to Roman_ (el problema inverso) y en cualquier sistema de codificación posicional con excepciones de substracción.
- **Lookup Table de Tamaño Constante como Núcleo de Parsers de Símbolos**: El patrón de precargar un mapa de símbolo → valor y consultarlo en $O(1)$ es la base de cualquier parser o decodificador de sistemas de codificación simbólica: conversión de bases numéricas, decodificación de Morse, evaluación de expresiones, tokenización léxica, y cualquier problema donde un conjunto fijo y pequeño de símbolos debe traducirse a valores computables. La tabla de 7 entradas de este problema escala conceptualmente a parsers de mayor complejidad.
