# Binary Tree Postorder Traversal

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos del árbol -> El bucle `while` procesa cada nodo exactamente **una vez**: cada nodo es extraído de la pila (`pop`) una única vez y su valor es añadido a `result` una única vez. La operación `.reverse()` / `array_reverse()` al final también es $O(N)$ pero no agrega un factor adicional a la complejidad asintótica; el coste total sigue siendo lineal.
- **Espacio**: $O(N)$ -> En el peor caso, la pila puede contener hasta $O(N)$ nodos (por ejemplo, en un árbol completamente completo donde un nivel entero de hojas se apila simultáneamente). El arreglo `result` crece hasta $N$ elementos. A diferencia del recorrido in-order cuyo stack es $O(H)$, el comportamiento de la pila en este algoritmo depende de la ramificación del árbol, alcanzando $O(N)$ en el peor caso general.

## Intuición y Enfoque

El recorrido **post-order** (izquierda → derecha → raíz) es el más difícil de implementar iterativamente de los tres recorridos clásicos, porque un nodo solo debe procesarse **después** de que ambos subárboles hayan sido completamente recorridos. Esto requiere "recordar" nodos para procesarlos en el futuro, creando dependencias hacia atrás en el flujo de ejecución.

La solución implementa la técnica del **Recorrido Pre-Order Modificado con Inversión Final (Modified Pre-Order + Reverse)**:

La idea pivote es reconocer una relación entre el recorrido post-order y el recorrido pre-order:

- **Pre-order estándar**: Raíz → Izquierda → Derecha
- **Pre-order modificado**: Raíz → Derecha → Izquierda _(se invierte el orden de apilamiento)_
- **Pre-order modificado + inversión**: Izquierda → Derecha → Raíz = **Post-order** ✓

El algoritmo aprovecha esta equivalencia en tres pasos:

1. **Recorrido Pre-Order Modificado**: Se ejecuta un recorrido idéntico al pre-order estándar, pero con el orden de apilamiento de los hijos **invertido**: primero se apila el hijo **izquierdo** y luego el **derecho** (en lugar del orden pre-order estándar que apila derecho-primero para visitar izquierdo-primero). Esto hace que el recorrido procese los nodos en el orden `Raíz → Derecha → Izquierda`, acumulando todos los valores en `result`.

2. **Acumulación en `result`**: En cada iteración del bucle, se extrae el tope de la pila, se añade su valor a `result`, y se apilan sus hijos (izquierdo primero, derecho segundo). El orden LIFO de la pila garantiza que el hijo derecho se extraiga antes que el izquierdo, produciendo el recorrido `Raíz → Derecha → Izquierda`.

3. **Inversión Final**: Al finalizar, `result.reverse()` / `array_reverse($result)` transforma la secuencia `Raíz → Derecha → Izquierda` en `Izquierda → Derecha → Raíz`, que es exactamente el orden post-order. La inversión es $O(N)$ y se realiza una sola vez.

Esta técnica convierte el problema más complejo de los recorridos iterativos en una variación trivial del pre-order, que es el más simple de implementar iterativamente.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Inicialización Condicional de la Pila**: Se usa `if (root) stack.push(root)` para apilar la raíz solo si no es `null`. Esto elimina la necesidad de una guarda explícita al inicio de la función: si `root` es `null`, la pila permanece vacía y el bucle `while (stack.length > 0)` no ejecuta ninguna iteración, retornando `[]` directamente.
  - **`const node = stack.pop()`**: La variable `node` se declara con `const` dentro del bucle, ya que el nodo extraído no se reasigna dentro de la iteración. Esto comunica inmutabilidad dentro del ciclo y permite optimizaciones del motor V8.
  - **`result.reverse()`**: El método `.reverse()` de `Array` en JavaScript muta el arreglo **in-place** y retorna la referencia al mismo arreglo (no crea una copia). Al ser el último paso antes del `return`, la mutación in-place es segura y evita la creación de un segundo arreglo de $N$ elementos, optimizando el uso de memoria.
  - **Verificación Truthy para Hijos**: `if (node.left) stack.push(node.left)` usa la falsyness de `null` para verificar la existencia del hijo de forma concisa, sin necesidad de `!== null`.

- **PHP**:
  - **Inicialización Directa de la Pila con `[$root]`**: PHP inicializa la pila directamente como `$stack = [$root]`, precedido de una guarda explícita `if ($root === null) return $result`. Este estilo separa el manejo del caso vacío del cuerpo principal del algoritmo, priorizando la legibilidad y el "early return" como patrón de control de flujo.
  - **`array_pop()` como Extracción LIFO**: PHP usa `array_pop($stack)` para extraer el último elemento, equivalente a `.pop()` de JavaScript. Internamente, PHP realiza esta operación en $O(1)$ ajustando el puntero interno del arreglo.
  - **`$stack[] = $node->left`**: PHP usa la sintaxis de appending (`$stack[] = ...`) en lugar de `array_push()` para añadir los hijos a la pila. Esta es la forma más idiomática y concisa, evitando la llamada de función de `array_push()`.
  - **`array_reverse($result)`**: A diferencia de JavaScript donde `.reverse()` muta el arreglo in-place, la función `array_reverse()` de PHP retorna un **nuevo arreglo** con los elementos en orden inverso, sin modificar el arreglo original `$result`. Esto implica la creación de un arreglo adicional de $N$ elementos, con un coste de memoria ligeramente mayor que la versión JS.
  - **Comparación Estricta `!== null`** para los hijos: PHP usa `$node->left !== null` en lugar de la verificación truthy implícita de JavaScript. En PHP, un nodo podría tener un valor `val = 0`, que sería falsy si se usara solo `if ($node->left)` sobre el valor (aunque `$node->left` es el objeto, no el valor). La comparación estricta con `null` es más explícita y robusta.

## Lecciones Clave

- **Recorrido Post-Order como Inversión del Pre-Order Modificado**: La relación simétrica entre post-order (`IZQ → DER → RAÍZ`) y el pre-order modificado con hijos invertidos (`RAÍZ → DER → IZQ`) es un insight algorítmico poderoso que transforma el recorrido más difícil en una variación trivial del más simple. Este truco de "recorrer en orden inverso y revertir el resultado" es aplicable en evaluación de expresiones de árbol (donde los operadores se procesan después de los operandos), cálculo de dependencias en grafos dirigidos acíclicos (topological sort), y liberación de memoria en sistemas de gestión de árbol donde los hijos deben liberarse antes que el padre.
- **La Inversión Final como Mecanismo de Reordenamiento $O(N)$**: Acumular resultados en un orden más fácil de producir iterativamente y luego invertirlos es un patrón de diseño recurrente en algoritmos que trabajan con estructuras donde el orden "natural" del procesamiento es el inverso del orden del output esperado. Este mismo patrón aparece en la generación de representaciones de números en base arbitraria (dígitos del menos al más significativo + reversión), la construcción de listas enlazadas en reversa, y cualquier recorrido post-order o bottom-up donde el resultado se construye "de atrás hacia adelante".
