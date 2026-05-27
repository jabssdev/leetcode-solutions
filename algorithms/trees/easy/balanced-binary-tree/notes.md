# Balanced Binary Tree

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos del árbol -> La función `checkHeight` realiza un recorrido **post-order** completo del árbol, visitando cada nodo exactamente **una vez**. En el instante en que se detecta un desbalance en cualquier subárbol, el valor centinela `-1` se propaga hacia arriba a través de las llamadas recursivas sin ejecutar ningún cómputo adicional (cortocircuito ascendente). Esto contrasta con el enfoque naïve de dos pasadas $O(N^2)$ donde se calcularía la altura de cada nodo por separado y luego se verificaría el balance, recalculando subárboles repetidamente.
- **Espacio**: $O(H)$ donde $H$ es la altura del árbol -> El consumo de memoria es proporcional a la profundidad máxima del Call Stack en la recursión, que equivale a la altura del árbol. En el peor caso (árbol completamente degenerado/lineal), $H = N$ y el espacio es $O(N)$. En el mejor caso (árbol perfectamente balanceado), $H = \log_2 N$ y el espacio es $O(\log N)$. No se crean estructuras de datos auxiliares adicionales más allá del Call Stack.

## Intuición y Enfoque

Un árbol binario está **balanceado en altura** si, para cada nodo, la diferencia entre las alturas de sus subárboles izquierdo y derecho es como máximo 1. El problema solicita verificar esta propiedad para el árbol completo.

El **enfoque naïve** consistiría en dos pasadas: primero calcular la altura de cada nodo (con una función recursiva), y luego verificar el balanceo en cada nodo verificando que `|height(left) - height(right)| ≤ 1`. Sin embargo, la función de altura se llamaría repetidamente para los mismos nodos desde diferentes niveles del árbol, produciendo una complejidad de $O(N^2)$ (o $O(N \log N)$ para árboles balanceados).

La solución implementa el **Patrón de Información Ascendente con Valor Centinela (Bottom-Up with Sentinel Value)**, que fusiona la verificación de balanceo y el cálculo de altura en **una única pasada recursiva post-order** $O(N)$:

La función `checkHeight` (interna en JS, método de clase en PHP) retorna:

- El **valor de retorno dual**: un entero con dos semánticas superpuestas:
  - **Un entero positivo** (incluyendo `0`): la altura real del subárbol enraizado en el nodo actual, indicando que dicho subárbol **está balanceado**.
  - **`-1` (centinela)**: indica que el subárbol enraizado en el nodo actual **no está balanceado**. Este valor se propaga hacia arriba automáticamente sin más cómputos.

El flujo de la recursión post-order:

1. **Caso base**: si el nodo es `null`, retorna `0` (altura de un árbol vacío, que está trivialmente balanceado).
2. Se calculan recursivamente `leftHeight` y `rightHeight`.
3. **Propagación del centinela**: si cualquiera de los dos hijos retornó `-1`, o si `|leftHeight - rightHeight| > 1`, se retorna `-1` inmediatamente.
4. En caso contrario, se retorna `max(leftHeight, rightHeight) + 1`: la altura real del subárbol actual.
5. En `isBalanced`, se verifica simplemente que `checkHeight(root) !== -1`.

La **elegancia del centinela `-1`** reside en que cortocircuita toda la rama de retorno: una vez detectado el desbalance en cualquier punto del árbol, el valor `-1` asciende automáticamente por todas las llamadas recursivas pendientes sin ejecutar comparaciones adicionales innecesarias.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Función Anidada (`checkHeight` como Closure)**: La función auxiliar se define como una arrow function dentro de `isBalanced` mediante `const checkHeight = (node) => { ... }`. Esto la encapsula completamente en el ámbito de `isBalanced`, haciéndola inaccesible desde el exterior y comunicando que es un detalle de implementación interna. En JavaScript, las funciones anidadas son closures y capturan el ámbito léxico del padre, aunque en este caso `checkHeight` no necesita capturar ninguna variable del ámbito externo.
  - **`Math.abs()` y `Math.max()`**: Se usan las funciones nativas del objeto `Math` para calcular el valor absoluto de la diferencia de alturas y la altura máxima. Ambas son $O(1)$ y altamente optimizadas en V8.
  - **Evaluación de Cortocircuito en la Condición**: La condición compuesta `leftHeight === -1 || rightHeight === -1 || Math.abs(leftHeight - rightHeight) > 1` evalúa primero los dos cheques del centinela. Si `leftHeight === -1`, el operador `||` cortocircuita y evita calcular `Math.abs(leftHeight - rightHeight)`, que sería matemáticamente irrelevante cuando ya se sabe que el árbol no está balanceado.
  - **Guarda con Falsy (`!node`)**: El caso base `if (!node) return 0` aprovecha la falsyness de `null` en JavaScript. Un nodo `null` (árbol vacío) es falsy, por lo que `!null === true` es el caso base de la recursión.

- **PHP**:
  - **`checkHeight` como Método de Clase con `$this->`**: A diferencia de JavaScript donde `checkHeight` es una función anidada local, PHP requiere que la función auxiliar sea un **método de la clase `Solution`**. Las llamadas recursivas usan `$this->checkHeight($node->left)` y `$this->checkHeight($node->right)`, siguiendo la semántica de invocación de métodos de instancia de PHP.
  - **`abs()` y `max()` como Funciones Globales**: PHP expone `abs()` y `max()` como funciones nativas del espacio de nombres global, sin necesidad del prefijo `Math.` de JavaScript. Ambas son wrappers de las funciones de la biblioteca estándar de C y operan en $O(1)$.
  - **Guarda con Falsy (`!$node`)**: Al igual que en JavaScript, PHP evalúa `!$node` como `true` cuando `$node` es `null`, ya que `null` es falsy en PHP. Esto produce el mismo caso base conciso: `if (!$node) return 0`.
  - **Isomorfismo Algorítmico con Diferencia de Alcance**: La lógica de `checkHeight` es matemáticamente idéntica entre ambas implementaciones. La única diferencia estructural significativa es el **alcance**: función anidada privada en JS vs. método de clase accesible públicamente en PHP. En PHP, `checkHeight` es un método público por defecto (sin modificador de visibilidad), lo cual podría mejorarse declarándolo `private` para encapsular correctamente el detalle de implementación.

## Lecciones Clave

- **Información Ascendente (Bottom-Up) con Valor de Retorno Dual como Fusión de Múltiples Verificaciones**: El patrón de usar un valor de retorno con **dos semánticas superpuestas** (un entero positivo = resultado válido, `-1` = señal de error) permite fusionar dos recorridos del árbol (cálculo de altura + verificación de balance) en uno solo, reduciendo la complejidad de $O(N^2)$ a $O(N)$. Este patrón es directamente aplicable en _Diameter of Binary Tree_ (retornar el diámetro máximo o el radio desde el nodo actual), _Binary Tree Maximum Path Sum_, y cualquier problema de árboles donde se necesiten propiedades de los subhijos para calcular una propiedad del nodo padre, y a su vez validar invariantes globales.
- **El Centinela como Mecanismo de Propagación de Error sin Excepciones**: El valor `-1` actúa como un **código de error propagado por retorno** a través de la recursión, análogo al manejo de errores sin excepciones en C o en sistemas embebidos. Cuando una función recursiva puede fallar y el fallo debe propagarse hacia arriba sin cómputo adicional, usar un valor centinela fuera del rango válido de retorno (en este caso, `-1` ya que las alturas son siempre ≥ `0`) es más eficiente que lanzar y capturar excepciones. Esta técnica se aplica en cualquier recorrido recursivo de árbol/grafo donde se necesita cortocircuitar la exploración ante una condición de fallo detectada en profundidad.
