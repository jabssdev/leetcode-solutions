# Same Tree

## Análisis de Complejidad

- **Tiempo**: $O(\min(N, M))$ donde $N$ y $M$ son el número de nodos de los árboles `p` y `q` respectivamente -> La recursión explora ambos árboles en **paralelo**, comparando nodo a nodo. Gracias al cortocircuito del operador `&&` (en el caso recursivo) y `||` (en la verificación de casos base), el recorrido **termina en el primer par de nodos que difieren**: si los árboles son distintos, no se procesa el resto. En el peor caso (árboles idénticos), se visitan todos los $\min(N, M)$ nodos (el árbol más pequeño limita la exploración). En el mejor caso, la diferencia se detecta en el primer nodo ($O(1)$).
- **Espacio**: $O(\min(H_p, H_q))$ donde $H_p$ y $H_q$ son las alturas de ambos árboles -> El Call Stack crece hasta la profundidad máxima del camino actualmente en exploración. Si los árboles difieren en estructura, la recursión termina antes de alcanzar la altura completa. En el peor caso (árboles idénticos y degenerados), el espacio es $O(N)$; para árboles balanceados, $O(\log N)$. No se crean estructuras de datos auxiliares.

## Intuición y Enfoque

El problema solicita verificar si dos árboles binarios son **estructuralmente idénticos** y tienen los **mismos valores en cada posición correspondiente**. Dos árboles son iguales si y solo si:

- Ambos son `null` (bases vacías idénticas), o
- Sus raíces tienen el mismo valor, **y** sus subárboles izquierdos son iguales, **y** sus subárboles derechos son iguales.

Esta definición es inherentemente recursiva: la igualdad de dos árboles se reduce a la igualdad de sus raíces más la igualdad de sus subárboles. La solución implementa exactamente esta definición mediante **DFS Recursivo de Comparación Paralela**, sin necesidad de serialización, hashing, ni estructuras auxiliares.

Ambas implementaciones unifican toda la lógica de comparación en **dos guardas y un retorno recursivo**, pero con filosofías de diseño distintas:

**JavaScript — Tres Casos Separados con Guardas Explícitas**:

1. `if (!p && !q) return true` → Ambos nulos: idénticos → `true`.
2. `if (!p || !q || p.val !== q.val) return false` → Exactamente uno es nulo (estructuras distintas), o ambos existen pero sus valores difieren → `false`. Esta segunda línea agrupa tres condiciones de fallo en una sola verificación.
3. `return isSameTree(p.left, q.left) && isSameTree(p.right, q.right)` → Ambos existen y tienen el mismo valor: verificar recursivamente los subárboles.

**PHP — Dos Casos Unificados con Lógica Simétrica**:

1. `if (!($p && $q)) return $p === $q` → Si al menos uno de los dos es `null` (o falsy), los árboles son iguales si y solo si **ambos** son `null`. Esta guarda compacta unifica los casos "ambos nulos" y "exactamente uno nulo" en una sola expresión: si `$p` es `null` y `$q` no lo es, `$p === $q` será `false`; si ambos son `null`, `$p === $q` será `true`.
2. `return $p->val === $q->val && isSameTree(left) && isSameTree(right)` → Si ambos existen (la guarda anterior no se activó), comparar valores y recursar.

Ambas estrategias son **correctas y equivalentes**, pero la PHP es más compacta al unificar los casos `null` en una sola expresión `$p === $q`.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Tres Líneas de Casos Base + Recursión**: La lógica se distribuye en tres declaraciones explícitas con flujo de control descendente. La primera (`!p && !q`) cubre el caso de éxito de ambos nulos. La segunda (`!p || !q || p.val !== q.val`) cubre todos los casos de fallo en una sola línea con evaluación de cortocircuito: si `!p` es `true`, las demás condiciones no se evalúan.
  - **Falsyness de `null` como Verificador de Existencia**: `!p` y `!q` explotan la falsyness de `null` en JavaScript. Un nodo `TreeNode` es siempre un objeto (truthy), y `null` es falsy. Esta distinción hace que `!p` sea semánticamente equivalente a `p === null` para nodos de árbol.
  - **Cortocircuito del `&&` en el Retorno Recursivo**: `isSameTree(p.left, q.left) && isSameTree(p.right, q.right)` garantiza que si el subárbol izquierdo no es igual (`false`), el subárbol derecho **nunca se evalúa**. Esto produce terminación temprana ante la primera diferencia detectada en cualquier rama izquierda de la comparación.
  - **Función Global Auto-Recursiva**: `isSameTree` es una función global (`var`) que se llama recursivamente por nombre. No requiere clase ni `this`.

- **PHP**:
  - **`!($p && $q)` como Unificador de Casos Nulos**: La expresión `!($p && $q)` es `true` cuando al menos uno de los dos argumentos es falsy (`null`). Cuando la guarda se activa, `$p === $q` retorna `true` solo si ambos son idénticos (`null === null`), y `false` si uno es `null` y el otro es un objeto (`null !== objeto`). Esta es una formulación más matemática y simétrica que las tres líneas separadas de JavaScript.
  - **Comparación Estricta `===` para Valores y para Nulos**: PHP usa `===` tanto para comparar los valores numéricos `$p->val === $q->val` como para la comparación de nulos `$p === $q` en la guarda. El operador `===` en PHP verifica tipo y valor, garantizando que `null === null` es `true` y `null === (object)TreeNode` es `false` sin coerción implícita.
  - **`$this->isSameTree()` como Método Recursivo de Instancia**: PHP requiere el prefijo `$this->` para llamadas recursivas dentro de la clase, a diferencia de JavaScript donde la función global se referencia directamente por nombre.
  - **Retorno en Línea con `&&` Multi-línea**: PHP usa un retorno multi-línea con `&&` para mejorar la legibilidad de la expresión de comparación: `$p->val === $q->val && isSameTree(left) && isSameTree(right)`. El mismo cortocircuito de `&&` aplica: si los valores difieren, los subárboles no se comparan.

## Lecciones Clave

- **Comparación Paralela Recursiva como Patrón para Igualdad Estructural**: El patrón de recorrer dos estructuras en paralelo, comparando nodo a nodo en la misma posición relativa, es la técnica canónica para verificar la **igualdad estructural** de cualquier árbol o grafo acíclico. Se aplica directamente en _Symmetric Tree_ (comparar el subárbol izquierdo con el espejo del derecho), _Subtree of Another Tree_ (verificar si un árbol es subárbol de otro), _Leaf-Similar Trees_, y cualquier problema de comparación de estructuras de datos recursivas. La clave es siempre: "¿son iguales las raíces? ¿Y los subproblemas correspondientes?".
- **La Guarda `!($p && $q)` → `$p === $q` como Patrón de Unificación de Casos Nulos**: La formulación PHP (`if (!($p && $q)) return $p === $q`) unifica en una sola expresión todos los casos donde al menos uno de los dos argumentos es `null`, retornando la respuesta correcta en ambas sub-situaciones (`null === null` → iguales, `null !== objeto` → distintos). Este patrón de "si al menos uno falta, son iguales solo si ambos faltan" es aplicable en cualquier comparación de dos estructuras potencialmente vacías: comparación de listas enlazadas nodo a nodo, verificación de subárboles, o cualquier algoritmo de doble puntero sobre estructuras opcionales.
