# Binary Tree Preorder Traversal

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos del árbol -> El bucle `while` procesa cada nodo exactamente **una vez**: cada nodo es extraído de la pila (`pop`) una única vez y su valor es añadido a `result` una única vez. Las operaciones de apilamiento de hijos son $O(1)$ amortizado. No existe ninguna operación de post-procesamiento adicional (como la inversión del post-order), por lo que el pre-order iterativo es el más eficiente en tiempo constante real entre los tres recorridos clásicos.
- **Espacio**: $O(H)$ en el promedio y $O(N)$ en el peor caso -> La pila contiene, en cualquier momento, los nodos del camino actual más los hijos derechos pendientes de cada nivel. En un árbol perfectamente balanceado, la pila tiene como máximo $O(\log N)$ elementos (altura del árbol). En un árbol completamente degenerado hacia la derecha, la pila puede acumular hasta $O(N)$ nodos. El arreglo `result` ocupa $O(N)$ de espacio, pero es el output esperado y no un coste auxiliar evitable.

## Intuición y Enfoque

El recorrido **pre-order** (raíz → izquierda → derecha) es el más intuitivo y **directo de implementar iterativamente** de los tres recorridos clásicos de árboles. A diferencia del in-order (que requiere descender hasta el fondo antes de procesar) o del post-order (que requiere procesar después de ambos subárboles), el pre-order procesa el nodo **inmediatamente** al encontrarlo, antes de visitar cualquier hijo.

La solución implementa el **Recorrido Pre-Order Iterativo con Pila Explícita (Iterative Pre-Order with Explicit Stack)**, que es la forma más limpia de simular la pila de llamadas de la recursión de forma iterativa:

El mecanismo central descansa en el **orden inverso de apilamiento de hijos**:

1. Se apila la **raíz** inicialmente si no es `null`.
2. En cada iteración del bucle:
   - Se extrae el tope de la pila (`pop`): este es el nodo a procesar ahora.
   - Se añade su valor a `result` (fase "raíz").
   - Se apila el hijo **derecho** primero (si existe).
   - Se apila el hijo **izquierdo** segundo (si existe).
3. Por la semántica LIFO de la pila, el hijo **izquierdo** (apilado último) será el próximo en extraerse, garantizando que el subárbol izquierdo se procese antes que el derecho.

La **invariante clave**: apilar derecho antes que izquierdo garantiza que el izquierdo sea procesado primero (LIFO), produciendo exactamente el orden `RAÍZ → IZQ → DER` del pre-order sin bucles anidados adicionales.

**Comparación con los otros recorridos iterativos**:

| Recorrido      | Complejidad del Algoritmo Iterativo | Técnica                                   |
| -------------- | ----------------------------------- | ----------------------------------------- |
| **Pre-order**  | Un bucle simple                     | Apilar `DER` luego `IZQ`                  |
| **In-order**   | Dos bucles anidados                 | Descender izquierda + pop + girar derecha |
| **Post-order** | Un bucle simple + reversión         | Pre-order modificado + `reverse()`        |

El pre-order es el recorrido de referencia sobre el cual se construyen los otros dos.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Inicialización Condicional de la Pila**: `if (root) stack.push(root)` apila la raíz solo si no es `null`, aprovechando la falsyness de `null`. Esto permite que la pila comience vacía para árboles vacíos, y el bucle `while (stack.length > 0)` no ejecuta ninguna iteración, retornando `[]` directamente sin una guarda explícita adicional.
  - **Orden de Apilamiento `right` → `left`**: La secuencia `if (node.right) stack.push(node.right); if (node.left) stack.push(node.left)` es el corazón del algoritmo. El hijo derecho se apila primero para que sea procesado **después** del izquierdo (LIFO). Este orden contraproducente intencionalmente es el que produce el orden pre-order correcto.
  - **`const node = stack.pop()`**: Se declara `node` con `const` dentro del bucle ya que el nodo extraído no se reasigna en la iteración actual. Las verificaciones de existencia de hijos usan la falsyness de `null` (`if (node.right)`, `if (node.left)`), siendo más concisas que las comparaciones explícitas.
  - **Sin `reverse()` Final**: A diferencia del post-order, el pre-order iterativo no requiere ninguna operación de post-procesamiento. Los valores se acumulan directamente en el orden correcto, haciendo este recorrido el más limpio y eficiente de implementar.

- **PHP**:
  - **Guarda Explícita con Early Return**: `if ($root === null) return $result` maneja el caso de árbol vacío con un retorno anticipado antes de inicializar la pila. Esta separación explícita del caso base es el estilo idiomático de PHP, equivalente a la inicialización condicional `if (root) stack.push(root)` de JavaScript pero con `return` en lugar de continuación.
  - **Inicialización Directa `$stack = [$root]`**: PHP inicializa el arreglo-pila con la raíz directamente mediante sintaxis de arreglo literal, equivalente a crear el arreglo vacío y hacer push de la raíz en un solo paso.
  - **`$stack[] = $node->right` / `$stack[] = $node->left`**: La sintaxis de appending idiomática de PHP es más concisa que `array_push()`. El orden `right` primero, `left` segundo es idéntico al de JavaScript, produciendo el mismo comportamiento LIFO que garantiza el orden pre-order.
  - **`!empty($stack)` como Guarda de Bucle**: `!empty($stack)` es el equivalente idiomático de PHP a `stack.length > 0` de JavaScript. `empty()` en PHP verifica si el arreglo está vacío de forma nativa y concisa.
  - **Comparación Estricta `!== null` vs Verificación Truthy**: PHP usa `$node->right !== null` en lugar de la verificación truthy implícita de JavaScript. En PHP, es necesario ser explícito ya que el objeto hijo podría tener propiedades con valores falsy (`val = 0`), aunque en este caso específico `$node->right` es el objeto `TreeNode` mismo (no su valor), por lo que la verificación truthy también funcionaría correctamente. La comparación estricta `!== null` es más robusta y semánticamente explícita.

## Lecciones Clave

- **El Pre-Order como Recorrido de Referencia: la Base de los Otros Dos**: El recorrido pre-order iterativo es el más simple de implementar correctamente y es la **piedra de toque** desde la cual se derivan los otros dos recorridos iterativos: el in-order agrega un bucle de descenso izquierdo antes del procesamiento, y el post-order es un pre-order con hijos en orden inverso más una reversión final. Dominar el pre-order iterativo con su invariante de "apilar derecho antes que izquierdo" es el prerequisito fundamental para entender todos los recorridos de árboles sin recursión.
- **El Orden Inverso de Apilamiento como Patrón para Controlar el Orden de Procesamiento LIFO**: La técnica de apilar elementos en el **orden inverso al orden de procesamiento deseado** es un patrón general aplicable siempre que se use una pila para simular un flujo de procesamiento secuencial. En pre-order: se quiere visitar izquierda antes que derecha, por lo que se apila derecha antes que izquierda. Este mismo principio aparece en algoritmos DFS donde el orden de exploración de vecinos importa, en evaluación de expresiones postfijas, y en simulación de algoritmos recursivos con pila explícita donde el orden de las sub-llamadas debe preservarse.
