# Invert Binary Tree — Technical Notes

## Análisis de Complejidad

- **Tiempo: `O(n)`** — La solución es un **DFS post-order** recursivo que visita cada nodo exactamente una vez. Para cada nodo se realiza un número constante de operaciones (swap de punteros y dos llamadas recursivas). No existe ningún camino de ejecución que visite un nodo más de una vez, por lo que el costo total es proporcional al número de nodos `n` del árbol.

- **Espacio: `O(h)`** — donde `h` es la altura del árbol. El único espacio auxiliar consumido es el **call stack de la recursión**, que crece hasta la profundidad máxima del árbol en la rama de mayor longitud. En el peor caso (árbol degenerado / lista enlazada), `h = n`, por lo que el espacio sería `O(n)`. En el caso promedio (árbol balanceado), `h = log n`, resultando en `O(log n)`. No se crean estructuras de datos adicionales en el heap.

---

## Intuición y Enfoque

La técnica utilizada es **DFS Post-Order Recursivo** con swap in-place de punteros hijos.

**Premisa clave:** Invertir un árbol binario significa que, para cada nodo, su subárbol izquierdo pase a ser el derecho y viceversa — aplicando esta transformación de forma recursiva a todos los nodos. El orden de procesamiento importa: se debe invertir primero de forma recursiva cada subárbol, y luego realizar el swap en el nodo actual (post-order). Si se hiciera en pre-order, se podría perder la referencia original al subárbol izquierdo antes de invertirlo.

**Lógica central:**
```
invertTree(node):
  1. Caso base: si node es null → retornar null.
  2. Recursión: invertir el subárbol derecho → resultado A.
  3. Recursión: invertir el subárbol izquierdo → resultado B.
  4. Asignar: node.left = A, node.right = B  [swap]
  5. Retornar node (la raíz del subárbol ya invertido).
```

**¿Por qué es óptima?** No existe un algoritmo mejor que `O(n)` para este problema: cualquier solución correcta debe visitar cada nodo al menos una vez para realizar el swap. Esta solución alcanza ese límite inferior teórico directamente. No requiere estructuras auxiliares como queues (BFS iterativo) ni stacks explícitos (DFS iterativo), delegando el manejo del estado en el call stack del lenguaje.

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** La solución completa se reduce a **una sola línea operativa** gracias a la **destructuring assignment** de ES6:
  ```js
  [root.left, root.right] = [invertTree(root.right), invertTree(root.left)];
  ```
  El lado derecho de la asignación crea un array temporal que evalúa **completamente** ambas expresiones (`invertTree(root.right)` e `invertTree(root.left)`) antes de que se produzca ninguna asignación al lado izquierdo. Esto garantiza que las referencias originales a `root.left` y `root.right` estén preservadas durante la evaluación recursiva, haciendo innecesaria una variable temporal intermedia. La elegancia de esta construcción es semánticamente equivalente a un swap atómico.

- **PHP:** Al no disponer de destructuring assignment de arreglos para intercambio de expresiones complejas en una sola operación segura, la solución requiere una **variable temporal explícita** `$temp`:
  ```php
  $temp = $root->left;
  $root->left  = $this->invertTree($root->right);
  $root->right = $this->invertTree($temp);
  ```
  El orden aquí es crítico: se guarda `$root->left` en `$temp` **antes** de sobreescribirlo con el resultado de `invertTree($root->right)`. Luego se pasa `$temp` (la referencia al subárbol izquierdo original) a la segunda llamada recursiva. Si se invirtiera el orden de las dos asignaciones, se perdería la referencia al subárbol izquierdo original y el resultado sería incorrecto.

> [!IMPORTANT]
> Este es el punto de divergencia más significativo entre ambas implementaciones. En JS, el array temporal del destructuring actúa como buffer atómico que resuelve el problema de aliasing de referencias. En PHP, el programador debe gestionar ese buffer manualmente con `$temp`. Ambas son correctas, pero la versión JS es más resistente a errores de orden en la asignación.

---

## Lecciones Clave

- **DFS Post-Order para transformaciones estructurales de árbol:** Cuando una operación sobre un árbol requiere modificar un nodo basándose en el estado de sus hijos (ya transformados), el traversal **post-order** (izquierda → derecha → raíz) es la elección natural. Aplicar este patrón en problemas como serialización de árboles, cálculo de alturas/diámetros, o cualquier transformación bottom-up donde el resultado del nodo depende de sus subárboles.

- **Destructuring como swap atómico en JavaScript:** El patrón `[a, b] = [expr_b, expr_a]` en JS no es solo azúcar sintáctico — resuelve el problema de aliasing de referencias al evaluar completamente el lado derecho antes de mutar el izquierdo. Es una herramienta de alta confiabilidad para swaps in-place en arrays, punteros de listas enlazadas y punteros de árboles, y debe preferirse sobre el patrón de variable temporal en JS moderno.
