# Path Sum

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos del árbol -> La recursión realiza un recorrido **DFS Pre-Order** sobre el árbol. En el **peor caso** (ningún camino de raíz a hoja suma `targetSum`), se visitan todos los $N$ nodos antes de determinar que no existe el camino. En el **mejor caso** (la primera hoja explorada satisface la condición), el algoritmo termina en $O(H)$ donde $H$ es la altura del árbol hacia la primera hoja. El operador `||` de cortocircuito asegura que si el subárbol izquierdo retorna `true`, el subárbol derecho **no se evalúa**, reduciendo el número de nodos visitados en casos favorables.
- **Espacio**: $O(H)$ donde $H$ es la altura del árbol -> El único espacio adicional es el **Call Stack de la recursión**, que crece hasta la profundidad del camino actual siendo explorado. En el peor caso (árbol degenerado lineal), $H = N$ y el espacio es $O(N)$. En el mejor caso (árbol perfectamente balanceado), $H = \lfloor \log_2 N \rfloor$ y el espacio es $O(\log N)$. No se crean estructuras de datos auxiliares (arreglos, mapas, colas) en ningún momento del recorrido.

## Intuición y Enfoque

El problema solicita determinar si existe un camino desde la raíz hasta alguna **hoja** cuya suma de valores sea exactamente `targetSum`. El problema se descompone naturalmente en subproblemas mediante la técnica de **reducción recursiva de la suma objetivo (Target Sum Reduction)**:

En lugar de acumular la suma desde la raíz hacia abajo y compararla al llegar a la hoja, el algoritmo **resta el valor del nodo actual del objetivo restante** en cada paso de descenso. Esto transforma el problema en: "¿existe en cualquier subárbol de este nodo un camino hasta una hoja que sume exactamente `targetSum - root.val`?". El estado de la recursión es compacto: solo el nodo actual y la suma restante.

La solución implementa **DFS Pre-Order Recursivo con Reducción de Suma** en tres casos:

1. **Caso Base 1 — Nodo nulo**: `if (!root) return false`. Si se alcanza un nodo `null`, no existe ningún camino de raíz a hoja que pase por aquí → retornar `false`. Esto también maneja el árbol completamente vacío.

2. **Caso Base 2 — Nodo hoja**: `if (!root.left && !root.right) return root.val === targetSum`. Si el nodo actual es una hoja (sin hijos), el único camino posible termina aquí. El camino es válido si y solo si `root.val === targetSum` (es decir, el valor restante acumulado desde arriba iguala el valor de la hoja actual). Este es el único punto donde se verifica la condición de éxito.

3. **Caso Recursivo**: `return hasPathSum(left, targetSum - root.val) || hasPathSum(right, targetSum - root.val)`. Si el nodo tiene al menos un hijo, se exploran recursivamente ambos subárboles (si existen) restando el valor actual del objetivo. El `||` garantiza que si cualquier subárbol contiene el camino, se retorna `true`.

**¿Por qué verificar la hoja explícitamente antes de recursar?** Si se recurriera directamente sin la verificación de hoja, el primer caso base (`!root → false`) manejaría los hijos nulos, pero habría un error: para un nodo con un solo hijo (por ejemplo, solo hijo izquierdo), el nodo derecho es `null` y `hasPathSum(null, remainder)` retornaría `false` correctamente, pero el nodo actual no es una hoja y tampoco debería considerarse como destino válido del camino. La verificación explícita de hoja (`!left && !right`) antes de la comparación garantiza que el camino **termine en una hoja real**, no en un nodo interno.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Función Global Auto-Recursiva**: `hasPathSum` se define como una función global (`var`) y se llama recursivamente a sí misma por nombre directamente (`hasPathSum(root.left, ...)`). JavaScript eleva las declaraciones `var` al ámbito de la función, por lo que la función es accesible en su propio cuerpo desde el primer momento.
  - **Guarda con Falsy `!root`**: La verificación `if (!root) return false` usa la falsyness de `null` en JavaScript. Un nodo `null` es falsy, por lo que `!null === true` activa el caso base. Esto es más conciso que `root === null`.
  - **Verificación de Hoja con Falsy `!root.left && !root.right`**: Mismo principio: ambos hijos son `null` (falsy), confirmando que el nodo es una hoja sin necesidad de comparaciones explícitas con `null`.
  - **Cortocircuito del `||`**: `hasPathSum(root.left, ...) || hasPathSum(root.right, ...)` aprovecha la evaluación de cortocircuito: si el subárbol izquierdo retorna `true`, el subárbol derecho nunca se evalúa, optimizando el caso de éxito temprano.
  - **Reducción Inline sin Variable Auxiliar**: La resta `targetSum - root.val` se calcula directamente en los argumentos de las llamadas recursivas, sin declarar una variable `remainingSum` intermedia. Esto es más conciso pero equivalente.

- **PHP**:
  - **Método de Instancia con `$this->`**: La función recursiva se declara como método de la clase `Solution`. Las llamadas recursivas requieren el prefijo `$this->hasPathSum(...)`, que es el mecanismo de auto-referencia de PHP orientado a objetos. No hay equivalente de la auto-referencialidad por nombre de función global de JavaScript en el contexto de una clase PHP.
  - **Comparaciones Estrictas `=== null`**: PHP usa `$root === null` y `$root->left === null && $root->right === null` con el operador de identidad estricta en lugar de la verificación truthy implícita de JavaScript. Esto es más explícito y previene coerciones inesperadas, aunque en este caso los objetos `TreeNode` nunca son falsy en PHP (solo los valores escalares como `0`, `""`, `[]`, o `null` son falsy).
  - **Variable Auxiliar `$remainingSum`**: PHP extrae la resta `$targetSum - $root->val` en una variable local `$remainingSum` antes de las llamadas recursivas. Esto mejora la legibilidad del código al nombrar explícitamente el concepto ("suma restante") y evita la doble evaluación de la expresión (aunque los compiladores modernos optimizan esto de todos modos).
  - **Isomorfismo Algorítmico Total**: Los tres casos (nulo, hoja, recursivo) son estructuralmente idénticos entre ambas implementaciones. La lógica de reducción de suma, la verificación de hoja y el retorno con `||` son matemáticamente equivalentes.

## Lecciones Clave

- **Reducción del Objetivo como Patrón para Problemas de Suma en Caminos**: La técnica de **reducir el objetivo en lugar de acumular la suma** (`targetSum - node.val` descendiendo vs. `sum + node.val` ascendiendo) es una formulación más elegante para problemas de suma en caminos porque el estado del subproblema es más compacto: solo se necesita "¿cuánto falta para llegar al objetivo?" en lugar de "¿cuánto llevamos acumulado Y cuál es el objetivo?". Este patrón se aplica directamente en *Path Sum II* (encontrar todos los caminos), *Path Sum III* (prefijos de cualquier nodo a cualquier descendiente), *Target Sum* (asignar signos a enteros), y cualquier problema de búsqueda de camino con condición de suma acumulada.
- **La Verificación de Hoja como Condición de Terminación Semántica, No Estructural**: Este problema ilustra que la condición de terminación de la recursión debe capturar la **semántica del dominio** ("un camino válido termina en una hoja"), no solo la **estructura del árbol** ("un nodo es nulo"). Verificar explícitamente `!left && !right` antes de comparar la suma garantiza que el camino termine en una hoja real y no en un nodo interno con un solo hijo. Este principio de "verificar la condición de éxito antes de delegar a los subproblemas" es fundamental en problemas de búsqueda recursiva con restricciones de terminación específicas del dominio.
