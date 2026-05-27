# Binary Tree Inorder Traversal

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos del árbol -> El algoritmo visita cada nodo del árbol exactamente **una vez**. Cada nodo es apilado (`push`) exactamente una vez cuando se desciende por la rama izquierda, y desapilado (`pop`) exactamente una vez cuando se procesa. Las operaciones `push`/`pop` sobre el arreglo-pila son $O(1)$ amortizado. El valor del nodo se añade al arreglo resultado una única vez por nodo. La complejidad temporal total es $O(N)$.
- **Espacio**: $O(H)$ donde $H$ es la altura del árbol -> La pila (`stack`) almacena, en cualquier momento dado, los nodos que forman el camino desde la raíz hasta el nodo más a la izquierda del subárbol actual. En el peor caso (árbol degenerado / lineal hacia la izquierda), la pila contiene $N$ nodos y el espacio es $O(N)$. En el mejor caso (árbol perfectamente balanceado), la pila tiene como máximo $\lfloor \log_2 N \rfloor + 1$ nodos y el espacio es $O(\log N)$. El arreglo `result` ocupa $O(N)$ de espacio adicional, pero es parte del output y no un coste auxiliar evitable.

## Intuición y Enfoque

El recorrido **in-order** (izquierda → raíz → derecha) de un árbol binario de búsqueda (BST) produce los valores en **orden ascendente**, lo que lo convierte en uno de los recorridos más fundamentales y útiles en algoritmos de árboles.

El enfoque **recursivo** es el más intuitivo para el recorrido in-order: `inorder(node.left)`, procesar `node`, `inorder(node.right)`. Sin embargo, la recursión utiliza el **Call Stack del sistema** implícitamente, con riesgo de _stack overflow_ para árboles muy profundos y sin control explícito del flujo.

La solución implementa el **Recorrido In-Order Iterativo con Pila Explícita (Iterative Inorder with Explicit Stack)**, que simula el Call Stack de la recursión con un arreglo gestionado manualmente, ofreciendo control total del flujo y sin riesgo de desbordamiento:

El algoritmo funciona mediante **dos bucles anidados** con roles semánticamente distintos:

1. **Bucle exterior** `while (root || stack.length > 0)`: continúa mientras queden nodos por procesar. La condición compuesta cubre dos casos: hay un nodo actual (`root` no es null) que debe explorarse, o hay nodos pendientes en la pila que aún no han sido procesados.

2. **Bucle interior** `while (root)`: desciende por la rama izquierda del nodo actual, apilando cada nodo encontrado. Este bucle implementa la fase "izquierda primero" del in-order: empuja todos los nodos del camino izquierdo al stack antes de procesar ninguno. Cuando `root` llega a `null`, hemos alcanzado el nodo más a la izquierda del subárbol actual.

3. **Procesamiento del tope**: se extrae el tope de la pila (`stack.pop()`), que es el nodo más a la izquierda no procesado aún. Se añade su valor a `result` (fase "raíz" del in-order). Luego se redirige `root = root.right` para explorar el subárbol derecho en la siguiente iteración del bucle exterior (fase "derecha" del in-order).

La invariante del algoritmo es elegante: **el nodo en el tope de la pila es siempre el próximo nodo a visitar en el orden in-order**, porque es el nodo más a la izquierda del subárbol que aún no ha sido visitado.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **`Array` como Pila con `push()`/`pop()`**: JavaScript utiliza su `Array` nativo como pila. `.push(node)` añade al final y `.pop()` extrae del final, implementando semántica LIFO en $O(1)$ amortizado. La variable `root` es **mutada directamente** a lo largo del algoritmo (`root = root.left`, `root = root.right`, `root = stack.pop()`), actuando como el "cursor" móvil del recorrido.
  - **Condición del Bucle Exterior con `||`**: `while (root || stack.length > 0)` combina dos condiciones: `root` es truthy cuando apunta a un nodo válido (objeto), y falsy cuando es `null`. `stack.length > 0` verifica si hay nodos pendientes en la pila. El operador `||` garantiza que el bucle continúe si cualquiera de las dos condiciones es verdadera.
  - **Mutación del Parámetro `root`**: La solución modifica directamente el parámetro de entrada `root`. En JavaScript, los parámetros de tipo objeto se pasan por referencia de valor (la referencia es copiada, no el objeto), por lo que `root = root.left` reasigna el puntero local sin afectar al árbol original. Esta es una convención válida en soluciones de LeetCode donde la mutación del árbol no está prohibida.

- **PHP**:
  - **`array_push()` vs `$stack[] = `**: PHP usa `array_push($stack, $root)` para apilar el nodo, que es semánticamente equivalente a `$stack[] = $root` (la forma idiomática más concisa). `array_push()` es más explícito en su intención de "añadir al tope de la pila" y es el equivalente directo de `.push()` de JavaScript.
  - **`array_pop()` como Extracción LIFO**: PHP usa `array_pop($stack)` para extraer el último elemento del arreglo, implementando la semántica LIFO equivalente a `.pop()` de JavaScript. Ambas funciones operan en $O(1)$ amortizado sobre arreglos de PHP.
  - **`!empty($stack)` vs `stack.length > 0`**: PHP usa `!empty($stack)` como la forma idiomática para verificar si un arreglo no está vacío, equivalente a `stack.length > 0` de JavaScript. `empty()` en PHP retorna `true` si el arreglo es vacío (o si la variable es `null`, `false`, `0`, `""`, o `[]`), por lo que `!empty()` confirma que el arreglo tiene al menos un elemento.
  - **`$result[] = $root->val`**: PHP usa la sintaxis de appending (`$result[] = $root->val`) para añadir el valor del nodo al arreglo resultado, equivalente a `result.push(root.val)` de JavaScript. Es la forma más idiomática y eficiente en PHP para añadir elementos al final de un arreglo.
  - **Isomorfismo Estructural Total**: La lógica de los dos bucles anidados, el mecanismo de descenso izquierdo, el procesamiento del tope y el giro a la derecha son completamente isomorfos entre ambas implementaciones. Las únicas diferencias son sintácticas.

## Lecciones Clave

- **Simulación del Call Stack Recursivo con Pila Explícita**: Cualquier algoritmo recursivo que use el Call Stack del sistema puede transformarse en un algoritmo iterativo equivalente usando una **pila explícita**. Esta transformación es especialmente importante en recorridos de árboles y grafos para: (1) evitar stack overflow en árboles muy profundos, (2) obtener control granular del flujo de ejecución (por ejemplo, para pausar/reanudar el recorrido), y (3) facilitar la conversión a generadores/iteradores. El recorrido in-order iterativo es el modelo de referencia para esta transformación y se extiende directamente a los recorridos pre-order y post-order iterativos.
- **El Recorrido In-Order de un BST como Ordenación Implícita**: El recorrido in-order de un **Árbol Binario de Búsqueda** (BST) produce los valores en orden ascendente, lo que equivale a un ordenamiento implícito de la estructura de datos. Este principio se aplica en validación de BSTs (_Validate BST_), búsqueda del k-ésimo elemento más pequeño (_Kth Smallest Element in a BST_), conversión de BST a arreglo ordenado, y en implementaciones de estructuras de datos ordenadas como `TreeMap` o `TreeSet`. Reconocer que "in-order de BST = secuencia ordenada" es una herramienta de razonamiento poderosa para diseñar soluciones eficientes.
