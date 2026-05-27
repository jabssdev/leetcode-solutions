# Minimum Depth of Binary Tree

## Análisis de Complejidad

- **Tiempo**: $O(N)$ en el peor caso, $O(1)$ en el mejor caso -> Ambas soluciones implementan un **BFS por Niveles con Terminación Temprana**. El BFS procesa los nodos nivel por nivel y termina en el **instante en que encuentra la primera hoja**. En el mejor caso (la raíz misma es una hoja, o la primera hoja está en el nivel 2), el algoritmo termina sin procesar la mayoría del árbol. En el peor caso (árbol degenerado o la única hoja está en el nivel más profundo), se procesan hasta $N$ nodos. Esta propiedad de **terminación temprana garantizada en el primer nodo hoja encontrado** es la ventaja definitoria del BFS sobre el DFS para este problema específico: el BFS garantiza que la primera hoja encontrada es la **más superficial**, mientras que el DFS no puede garantizarlo sin explorar múltiples ramas.
- **Espacio**: $O(W)$ donde $W$ es el ancho máximo del árbol -> La cola almacena, en cualquier momento, los nodos del nivel actual siendo procesado. El ancho máximo $W$ es $O(N)$ para árboles perfectamente balanceados (el nivel de las hojas tiene $\lceil N/2 \rceil$ nodos). Sin embargo, dado que el BFS termina en la primera hoja encontrada, en la práctica la cola nunca acumula más nodos que los del nivel de la primera hoja, que en un árbol balanceado se alcanza mucho antes del nivel más ancho.

## Intuición y Enfoque

El problema solicita la **profundidad mínima**: el número de nodos en el camino más corto desde la raíz hasta **cualquier nodo hoja**. Un nodo hoja se define como un nodo sin hijos izquierdo ni derecho.

**La trampa crítica del DFS para este problema**: Un enfoque DFS recursivo naïve `1 + min(depth(left), depth(right))` tiene un **error semántico sutil** para nodos con un solo hijo. Considérese un nodo que tiene hijo izquierdo pero no hijo derecho: `min(depth(left), depth(null))` = `min(k, 0)` = `0`, haciendo que el algoritmo retorne `1` (profundidad del nodo actual), cuando en realidad ese nodo no es una hoja y la profundidad mínima debería computarse solo por el subárbol izquierdo. El DFS correcto para mínima profundidad requiere manejar explícitamente los casos de nodos con un solo hijo, añadiendo complejidad lógica.

El **BFS por Niveles con Terminación Temprana** elimina este problema completamente: la primera vez que el BFS encuentra un nodo sin hijos (una hoja verdadera), **ese nodo está garantizado estar en el nivel más superficial**, porque el BFS procesa los nodos estrictamente de menor a mayor profundidad. No es necesario ningún tratamiento especial de nodos con un solo hijo.

El algoritmo:

1. Se inicializa la cola con la raíz y `depth = 1` (la raíz está en el nivel 1).
2. En cada iteración del bucle exterior, se captura `levelSize`: el número de nodos en el nivel actual.
3. El bucle interior procesa los `levelSize` nodos del nivel actual uno por uno:
   - Si el nodo actual **no tiene hijos** (es hoja): `return depth` inmediatamente. Esta es la profundidad mínima garantizada.
   - Si tiene hijos, se encolan para el siguiente nivel.
4. Tras procesar todos los nodos del nivel, se incrementa `depth`.

El **orden diferente de `depth++`** respecto al problema de profundidad máxima es revelador: aquí `depth` se inicializa en `1` (raíz en nivel 1) y se incrementa **después** de procesar cada nivel, porque si la raíz misma es hoja, se retorna `1` antes del incremento.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **`head` como Puntero de Cola para Dequeue $O(1)$**: La diferencia más técnicamente significativa respecto al problema de profundidad máxima es que esta solución **evita `queue.shift()`** (que es $O(N)$). En su lugar, implementa una **cola eficiente mediante puntero de índice**: `let head = 0` es el índice del "frente" de la cola, y `queue[head++]` extrae el nodo actual avanzando el puntero sin desplazar el arreglo. El `levelSize` se calcula como `queue.length - head` (elementos disponibles desde el puntero hasta el final del arreglo). Esto garantiza que todas las operaciones de encolar/desencolar sean $O(1)$ amortizado.
  - **`depth` inicializado en `1`**: A diferencia del problema de profundidad máxima donde `depth` empieza en `0` y se incrementa antes de procesar cada nivel, aquí `depth = 1` (la raíz cuenta) y el incremento ocurre **después** del bucle interior. Esto permite que el retorno anticipado al encontrar una hoja retorne el valor correcto sin necesidad de `depth + 1`.
  - **`depth++` al final del bucle exterior**: Posicionado después del bucle interior, el incremento solo ocurre si ningún nodo del nivel actual era una hoja. Si se encontró una hoja, la función ya habrá retornado.
  - **Terminación Temprana `if (!node.left && !node.right) return depth`**: La condición de hoja usa la falsyness de `null` para verificar la ausencia de hijos de forma concisa. Este `return` dentro del bucle interior es el mecanismo de terminación temprana que hace al BFS óptimo para este problema.

- **PHP**:
  - **`SplQueue` como Cola FIFO Eficiente $O(1)$**: PHP usa la clase nativa `SplQueue` de la Standard PHP Library (SPL) en lugar de un arreglo con `array_shift()` (que sería $O(N)$). `SplQueue` implementa una cola doblemente enlazada con enqueue/dequeue en $O(1)$ garantizado. Sus métodos clave son `enqueue($node)` (equivalente a `push`/`queue[head++]`), `dequeue()` (equivalente a pop del frente en $O(1)$), `count()` (número de elementos actuales, equivalente a `queue.length - head`), e `isEmpty()` (condición de bucle equivalente a `head < queue.length`).
  - **`SplQueue::count()` para `levelSize`**: `$queue->count()` retorna el número de elementos actualmente en la cola en $O(1)$, equivalente al cálculo `queue.length - head` de JavaScript. Dado que `SplQueue` gestiona internamente un puntero de frente, `count()` refleja exactamente los elementos disponibles para dequeue.
  - **Isomorfismo Algorítmico con Diferencia de Estructura de Datos**: La lógica del BFS por niveles (captura de `levelSize`, verificación de hoja, encolado de hijos, incremento de `depth` al final) es **idéntica** entre ambas implementaciones. La diferencia exclusiva es la estructura de datos de cola: arreglo con puntero manual `head` (JS) vs. `SplQueue` nativa (PHP). Ambas logran la misma complejidad $O(1)$ por operación de enqueue/dequeue.
  - **Falsyness de `null` para verificar hijos**: Al igual que en JavaScript, `!$node->left && !$node->right` aprovecha la falsyness de `null` en PHP para verificar la ausencia de hijos izquierdo y derecho sin comparaciones explícitas `=== null`.

## Lecciones Clave

- **BFS como Garantía de Optimalidad para el Camino Mínimo**: El BFS por niveles es el algoritmo **óptimo garantizado** para cualquier problema de "mínima distancia desde el origen" en grafos o árboles no ponderados, porque explora los nodos en orden estrictamente creciente de distancia desde la fuente. La primera vez que encuentra el objetivo, ese es el camino más corto posible. Esta propiedad, que el DFS no puede garantizar sin exploración completa, hace al BFS la elección canónica para _Minimum Depth of Binary Tree_, _Word Ladder_, _Shortest Path in Binary Matrix_, _Jump Game_, y cualquier problema de "menor número de pasos/niveles para alcanzar un estado objetivo".
- **Cola con Puntero de Índice vs. `SplQueue` / Deque Nativa: Implementación Eficiente de FIFO en Lenguajes sin Cola Nativa**: JavaScript no tiene una estructura de cola FIFO nativa eficiente; la solución idiomática de usar `Array.shift()` es $O(N)$, haciendo que implementaciones naïve de BFS sean $O(N^2)$. El patrón de **puntero de índice sobre arreglo** (`head`) simula una dequeue en $O(1)$ a expensas de no liberar memoria de los elementos procesados (el arreglo crece hasta $N$). PHP resuelve esto con `SplQueue`, que es una cola enlazada nativa $O(1)$. En entornos de producción donde el BFS es un cuello de botella, conocer las alternativas eficientes para la cola es una competencia de ingeniería crítica.
