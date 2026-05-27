# Symmetric Tree

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número total de nodos del árbol -> El algoritmo realiza un recorrido recursivo de manera simultánea en ambos subárboles (izquierdo y derecho). En el peor de los casos (un árbol completamente simétrico), se visitará cada nodo exactamente una vez para validar su simetría.
- **Espacio**: $O(H)$ donde $H$ es la altura del árbol -> No se utilizan estructuras de datos adicionales. El espacio en memoria está determinado únicamente por el Call Stack recursivo. En el peor caso (un árbol degenerado o tipo lista), la altura $H = N$, resultando en $O(N)$; en el mejor caso (un árbol perfectamente balanceado), la altura $H = \log(N)$, resultando en $O(\log(N))$.

## Intuición y Enfoque

El problema nos pide verificar si un árbol binario es simétrico (es decir, una imagen especular de sí mismo). La intuición principal radica en descomponer la simetría del árbol en la comparación de dos subárboles. Un árbol es simétrico si y solo si su subárbol izquierdo es un espejo de su subárbol derecho.

Para resolver esto de forma óptima, se utiliza un enfoque de **Dividir y Vencer (Recursión con DFS)** a través de una función auxiliar `isMirror(left, right)`. La lógica del espejo se define por tres condiciones que deben cumplirse simultáneamente:

1. Las raíces de ambos subárboles son ambas `null` (caso base exitoso).
2. Ambas raíces existen y sus valores son idénticos.
3. El subárbol izquierdo del nodo izquierdo es un espejo del subárbol derecho del nodo derecho (`isMirror(left.left, right.right)`), **y** el subárbol derecho del nodo izquierdo es un espejo del subárbol izquierdo del nodo derecho (`isMirror(left.right, right.left)`).

Este enfoque es óptimo frente a la fuerza bruta (como serializar el árbol y verificar palíndromos) porque permite una **evaluación perezosa o cortocircuitada (short-circuit evaluation)**: tan pronto como se detecta una asimetría estructural o de valor, la recursión finaliza inmediatamente, ahorrando tiempo de procesamiento.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - Se define la función auxiliar `isMirror` como una función de expresión en el ámbito local (`var isMirror = function(...)`), lo cual restringe su visibilidad y evita contaminar el espacio de nombres de la clase o módulo.
  - Utiliza el operador de desigualdad estricta `!==` para comparar los valores de los nodos (`left.val !== right.val`), garantizando que tanto el tipo como el valor coincidan de manera segura sin coerción implícita de tipos.
  - La verificación de valores nulos se realiza de manera concisa mediante la coerción de verdad (`!left && !right`), aprovechando la naturaleza de JS para evaluar punteros a objetos o referencias `null`.

- **PHP**:
  - Se implementa como métodos de una clase orientada a objetos (`Solution`). El método auxiliar `isMirror` se expone en la clase y se invoca contextualmente utilizando `$this->isMirror(...)`.
  - Emplea el operador de comparación estricta `!==` y el operador de objeto `->` para acceder a las propiedades de los nodos (`$left->val !== $right->val`), manejando referencias seguras a objetos de tipo `TreeNode`.
  - La lógica de cortocircuito se beneficia de los tipos estrictamente definidos en PHP para evitar errores de acceso a miembros cuando una variable puede ser `null`.

## Lecciones Clave

- **Descomposición en Espejos Simultáneos**: Este patrón de diseño recursivo para comparar dos punteros independientes a lo largo de caminos inversos (ir a la izquierda en uno mientras se va a la derecha en el otro) es fundamental para resolver problemas de emparejamiento estructural o isomorfismo de grafos.
- **Cortocircuito Recursivo como Optimización**: Cuando diseñe validaciones de consistencia o equivalencia sobre estructuras jerárquicas complejas, siempre ordene las condiciones de salida para que los casos fallidos y los límites nulos se evalúen al inicio (`if (!left || !right ...)`). Esto minimiza el overhead del stack de llamadas y previene errores de segmentación u operaciones de puntero nulo (_null pointer exceptions_).
