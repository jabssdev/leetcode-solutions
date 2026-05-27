# Maximum Depth of Binary Tree

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos del árbol -> La solución JavaScript implementa un **BFS por Niveles (Level-Order BFS)**. El bucle exterior procesa cada nivel del árbol exactamente una vez, y el bucle interior procesa cada nodo de ese nivel exactamente una vez. Cada nodo es encolado (`push`) y desencolado (`shift`) exactamente una vez a lo largo de todo el algoritmo. El número total de operaciones es proporcional a $N$, resultando en $O(N)$.

  > [!NOTE]
  > **Sobre el coste de `queue.shift()`**: En JavaScript, `Array.prototype.shift()` es $O(N)$ en el tamaño actual del arreglo (requiere reindexar todos los elementos restantes). Para inputs grandes, esto hace que la complejidad real del BFS con `Array` sea $O(N^2)$ en el peor caso. La solución óptima usaría una estructura de cola FIFO real (por ejemplo, con un puntero de índice sobre el arreglo) para preservar la complejidad $O(N)$. En la práctica de LeetCode, el overhead es aceptable para los límites del problema.

- **Espacio**: $O(W)$ donde $W$ es el ancho máximo del árbol -> La cola del BFS contiene en todo momento los nodos del nivel actual siendo procesado. En el peor caso, el nivel más ancho de un árbol perfectamente balanceado contiene $\lceil N/2 \rceil$ nodos (el nivel de las hojas), por lo que el espacio de la cola es $O(N/2) = O(N)$. Para árboles degenerados (lineales), el nivel más ancho tiene 1 nodo y la cola es $O(1)$. En general, $W$ es $O(N)$ en el peor caso.

## Intuición y Enfoque

El problema solicita la **profundidad máxima** de un árbol binario, definida como el número de nodos en el camino más largo desde la raíz hasta una hoja.

Existen dos familias de enfoque:

**Enfoque DFS Post-Order Recursivo** (el más intuitivo): la profundidad de un nodo es `1 + max(depth(left), depth(right))`. El caso base es que un nodo `null` tiene profundidad `0`. Esta recursión recorre el árbol en post-order (calcula las profundidades de los hijos antes de la del padre) y produce el resultado en $O(N)$ tiempo y $O(H)$ espacio de Call Stack.

**Enfoque BFS por Niveles** (implementado en JavaScript): en lugar de pensar en la profundidad como una propiedad calculada recursivamente desde abajo, se piensa en ella como el **número de niveles del árbol**, que es exactamente lo que el BFS por niveles cuenta de forma natural:

1. Se inicializa la cola con la raíz y `depth = 0`.
2. En cada iteración del bucle exterior, se captura el tamaño actual de la cola (`levelSize`): este es el número de nodos en el nivel actual.
3. Se incrementa `depth` en `1` (el nivel actual se cuenta).
4. El bucle interior procesa exactamente `levelSize` nodos del nivel actual, encolando sus hijos (que forman el siguiente nivel).
5. Al terminar el bucle exterior, `depth` contiene el número total de niveles = la profundidad máxima.

La invariante del algoritmo: **al inicio de cada iteración del bucle exterior, la cola contiene exactamente todos los nodos del siguiente nivel a procesar**. El número de iteraciones del bucle exterior es exactamente la altura del árbol.

**¿Por qué BFS en lugar de DFS?** El BFS por niveles ofrece una ventaja conceptual cuando el problema está formulado en términos de "niveles" o "distancia desde la raíz": la profundidad máxima se obtiene naturalmente como el contador de iteraciones del bucle exterior, sin necesidad de propagar alturas desde las hojas hacia la raíz.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **BFS Iterativo con `Array` como Cola**: JavaScript implementa la cola FIFO del BFS usando un `Array` nativo con `.push()` para encolar al final y `.shift()` para desencolar del frente. Como se menciona arriba, `.shift()` es $O(N)$; una optimización sería usar un puntero `head` sobre el arreglo para simular una dequeue eficiente.
  - **`const levelSize = queue.length`**: La captura del tamaño de la cola **antes** del bucle interior es el mecanismo clave del BFS por niveles. En el momento en que se evalúa `queue.length`, la cola contiene exactamente los nodos del nivel actual. Si se evaluara `queue.length` en la condición del bucle interior directamente, el tamaño cambiaría a medida que se encolasen los hijos, mezclando niveles.
  - **`depth++` antes del bucle interior**: El incremento del contador de profundidad ocurre al comienzo del procesamiento de cada nivel, antes de que los hijos sean encolados. Esto garantiza que incluso el nivel de la raíz (nivel 1) sea contado correctamente.
  - **Guarda `if (!root) return 0`**: La verificación inicial del árbol vacío evita que la cola se inicialice con `null`, lo que produciría un error al intentar acceder a `.left` y `.right` de un nodo `null` en el bucle.

- **PHP**:
  > [!WARNING]
  > La solución PHP (`solution.php`) tiene el cuerpo del método `maxDepth()` **vacío**. No hay una implementación para documentar. El enfoque natural en PHP para este problema sería la **recursión DFS Post-Order**, que es el complemento algorítmico del BFS iterativo de JavaScript:
  >
  > ```php
  > function maxDepth($root) {
  >     if ($root === null) return 0;
  >     return 1 + max($this->maxDepth($root->left), $this->maxDepth($root->right));
  > }
  > ```
  >
  > Este enfoque recursivo usa `max()` (función nativa PHP de valor absoluto máximo) como equivalente de `Math.max()` de JavaScript, y la recursión sobre `$root->left` / `$root->right` como equivalente al BFS con cola. Su complejidad es $O(N)$ tiempo y $O(H)$ espacio de Call Stack.

  **Contrastes que habrían surgido con una implementación completa**:
  - **DFS Recursivo (PHP) vs. BFS Iterativo (JS)**: El DFS produce la misma respuesta que el BFS para la profundidad máxima, pero con diferentes características de espacio: $O(H)$ para DFS (altura del árbol) vs. $O(W)$ para BFS (ancho máximo). Para árboles balanceados: $O(\log N)$ vs. $O(N)$.
  - **`max()` en PHP** es una función global equivalente a `Math.max()` en JavaScript, aplicable directamente a dos valores escalares.
  - **Cola en PHP**: Para un BFS en PHP, se usaría `array_shift()` (equivalente a `.shift()` de JS, también $O(N)$) o la clase `SplQueue` de la SPL para una cola FIFO eficiente $O(1)$.

## Lecciones Clave

- **BFS por Niveles como Contador Natural de Profundidad**: El patrón de **BFS con captura de `levelSize` antes del bucle interior** es el mecanismo estándar para procesar árboles nivel por nivel y razonar sobre propiedades que son naturalmente "por nivel": profundidad máxima, anchura máxima, valores de cada nivel, conectar nodos del mismo nivel (*Populating Next Right Pointers*), encontrar el nodo más a la derecha de cada nivel (*Binary Tree Right Side View*). Internalizar este patrón como una plantilla es esencial: la variable `levelSize` es siempre la clave que separa un nivel del siguiente.
- **DFS vs. BFS para Profundidad: el Trade-off de Espacio $O(H)$ vs. $O(W)$**: Ambos enfoques son correctos para calcular la profundidad máxima, pero con perfiles de espacio distintos. El DFS recursivo usa $O(H)$ de Call Stack (óptimo para árboles balanceados anchos, donde $H = O(\log N) \ll W = O(N)$). El BFS usa $O(W)$ de espacio de cola (óptimo para árboles degenerados profundos, donde $W = O(1) \ll H = O(N)$). Elegir entre DFS y BFS basándose en la forma esperada del árbol (balanceado vs. degenerado) es un ejercicio de diseño de algoritmos orientado a la arquitectura del dato de entrada.
