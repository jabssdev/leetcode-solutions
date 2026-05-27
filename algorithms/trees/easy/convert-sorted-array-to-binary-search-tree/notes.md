# Convert Sorted Array to Binary Search Tree

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud del arreglo `nums` -> Ambas soluciones crean exactamente un nodo `TreeNode` por cada elemento del arreglo de entrada: la función recursiva (`buildBST` en JS / `sortedArrayToBST` en PHP) es llamada exactamente $N$ veces para subproblemas no vacíos, más $N + 1$ veces para los casos base (`left > right` / `empty($nums)`). Cada llamada realiza trabajo constante $O(1)$ de construcción del nodo y cálculo del punto medio. La complejidad temporal total es $O(N)$.

  > [!NOTE]
  > **Diferencia entre implementaciones**: La versión PHP usa `array_slice()` en cada llamada recursiva, que crea una copia del subarreglo en $O(K)$ donde $K$ es el tamaño del slice. El coste total de todos los `array_slice()` es $O(N \log N)$ (cada elemento es copiado aproximadamente $\log N$ veces, una por cada nivel de profundidad de la recursión). La versión JS opera sobre índices del arreglo original en $O(1)$ por llamada, manteniendo una complejidad temporal estrictamente $O(N)$.

- **Espacio**: $O(\log N)$ para JS y $O(N \log N)$ para PHP (espacio auxiliar, sin contar el árbol de salida) -> En JavaScript, la función `buildBST` opera sobre índices del arreglo original sin copias. El Call Stack crece hasta la altura del BST resultante, que es $\lfloor \log_2 N \rfloor + 1$ niveles (árbol perfectamente balanceado). El espacio del Call Stack es $O(\log N)$. En PHP, cada llamada a `sortedArrayToBST` crea un subarreglo con `array_slice()`, por lo que en cualquier rama de la recursión hay $O(\log N)$ subarreglos vivos simultáneamente con tamaños $N, N/2, N/4, \ldots, 1$, sumando $O(N)$ de espacio en el peor camino de la pila. El Call Stack tiene $O(\log N)$ frames con subarreglos de tamaño decreciente.

## Intuición y Enfoque

El problema solicita construir un **Árbol Binario de Búsqueda (BST) Balanceado en Altura** a partir de un arreglo de enteros ordenado en forma ascendente. Un BST válido tiene la propiedad de que todos los valores en el subárbol izquierdo de un nodo son menores que el nodo, y todos en el subárbol derecho son mayores.

El insight fundamental es la **correspondencia directa entre búsqueda binaria y estructura de BST**: el elemento que debe ser la **raíz** de un BST balanceado es exactamente el **elemento del medio** del arreglo ordenado. Esto garantiza que el número de nodos en el subárbol izquierdo y el derecho difieran en como máximo 1, produciendo un árbol perfectamente balanceado en altura.

Ambas soluciones implementan la misma idea mediante **Divide y Vencerás (Divide and Conquer) Recursivo**:

1. **Caso Base**: subproblema vacío → retornar `null`.
2. **Divide**: calcular el índice del elemento medio (`mid`) del subarreglo actual. Este elemento se convierte en la raíz del subárbol actual.
3. **Vence**:
   - Construir recursivamente el subárbol izquierdo con los elementos **a la izquierda** del medio.
   - Construir recursivamente el subárbol derecho con los elementos **a la derecha** del medio.
4. **Combina**: asignar los subárboles construidos como hijo izquierdo y derecho del nodo raíz actual, y retornarlo.

El árbol resultante tiene altura $\lfloor \log_2 N \rfloor + 1$ y es un **Height-Balanced BST** (equivalente a un árbol AVL perfectamente construido).

**Diferencia de estrategia entre implementaciones**:

- **JavaScript** pasa índices `(left, right)` como límites del subarreglo sobre el arreglo original compartido, evitando copias de datos.
- **PHP** pasa subarreglos físicos a través de `array_slice()`, adoptando un enfoque más funcional pero con mayor coste de memoria.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Función `buildBST` como Closure Anidada**: La función auxiliar se define como una arrow function dentro de `sortedArrayToBST`, capturando el arreglo `nums` del ámbito léxico externo a través del mecanismo de closure. Esto elimina la necesidad de pasar `nums` como parámetro en cada llamada recursiva, reduciendo la firma de la función a solo los índices `(left, right)`.
  - **Cálculo del Medio Overflow-Safe**: `mid = left + Math.floor((right - left) / 2)` en lugar de `Math.floor((left + right) / 2)`. Para valores grandes de `left` y `right`, la suma podría exceder el rango de `Number.MAX_SAFE_INTEGER`. La formulación overflow-safe es la convención estándar de la búsqueda binaria.
  - **Acceso por Índice `nums[mid]`**: La función opera sobre el arreglo original sin ninguna copia, usando únicamente los índices `left` y `right` como límites del subarreglo virtual actual. Esto hace que el espacio auxiliar sea exclusivamente el Call Stack ($O(\log N)$).
  - **`new TreeNode(nums[mid])`**: Se instancia el nodo directamente con el valor del medio. Las asignaciones `node.left` y `node.right` se realizan después de las llamadas recursivas, siguiendo el patrón post-order de construcción del árbol (los hijos se construyen antes de ser asignados al padre).

- **PHP**:
  - **Estrategia de Subarreglos con `array_slice()`**: PHP implementa la recursión pasando **subarreglos físicos** en lugar de índices. `array_slice($nums, 0, $mid)` crea un nuevo arreglo PHP con los primeros `$mid` elementos, y `array_slice($nums, $mid + 1)` crea otro con los elementos desde `$mid + 1` hasta el final. Esta estrategia es más idiomática en PHP funcional pero tiene un coste de memoria de $O(N \log N)$ en total.
  - **`count($nums)` en lugar de longitud por índices**: PHP calcula el índice del medio como `floor(count($nums) / 2)` sobre el arreglo local, que puede tener cualquier tamaño. Dado que PHP pasa arreglos por valor (copy-on-write), cada llamada recursiva recibe su propio subarreglo independiente.
  - **Reutilización del método principal**: PHP llama a `$this->sortedArrayToBST()` recursivamente en lugar de una función auxiliar separada, aprovechando que `array_slice()` produce subarreglos que tienen los mismos índices lógicos que el arreglo completo (el medio de un subarreglo de tamaño $K$ es el elemento en la posición $K/2$). Esto simplifica el código al no requerir una función auxiliar, a expensas del coste de copia de `array_slice()`.
  - **`empty($nums)` como Caso Base**: PHP usa `empty($nums)` para verificar si el subarreglo está vacío, lo cual es equivalente al caso base `left > right` de JavaScript. `empty()` retorna `true` si el arreglo tiene cero elementos.
  - **`floor(count($nums) / 2)`**: PHP usa `floor()` para la división entera en lugar de `intdiv()`. Para `count($nums)` par, el medio corresponde al segundo elemento de la mitad izquierda (índice $N/2$), que junto con `array_slice($nums, 0, $mid)` (los primeros `$mid` elementos) y `array_slice($nums, $mid + 1)` produce una distribución [izquierda: $\lfloor N/2 \rfloor$ elementos, raíz: 1, derecha: $\lceil N/2 \rceil - 1$ elementos].

## Lecciones Clave

- **El Punto Medio Recursivo como Constructor de Estructuras Balanceadas**: El patrón de "seleccionar el elemento central como raíz y recursar sobre las mitades" es el algoritmo universal de construcción de cualquier estructura de datos balanceada desde un arreglo ordenado. Se aplica directamente en la construcción de Árboles de Segmento (_Segment Trees_), Árboles Indexados Binarios (Fenwick Trees), Binary Search Trees desde listas ordenadas, y en la comprensión de por qué la búsqueda binaria es $O(\log N)$: el árbol implícito de decisiones de la búsqueda binaria **es** exactamente el BST que este algoritmo construye.
- **Índices vs. Copias de Subarreglos como Trade-off de Espacio vs. Legibilidad**: La diferencia entre la estrategia JS (índices sobre el arreglo original, $O(\log N)$ de espacio) y la PHP (`array_slice()`, $O(N \log N)$ de espacio) ilustra un trade-off de diseño frecuente en algoritmos de divide y vencerás: pasar límites de índices es más eficiente en memoria pero requiere una función auxiliar con firma extendida; pasar subarreglos físicos es más legible y funcional pero costoso en memoria. En entornos donde la eficiencia de memoria es crítica (sistemas embebidos, conjuntos de datos muy grandes), los índices son siempre preferibles. En la práctica de programación cotidiana, la legibilidad puede justificar el coste adicional de las copias.
