# Count Complete Tree Nodes — Technical Notes

## Análisis de Complejidad

- **Tiempo: `O(log²n)`** — Esta es la complejidad característica y no trivial de esta solución. En cada llamada recursiva a `countNodes`, se calculan `leftHeight` y `rightHeight`, cada uno en `O(log n)` (descenso iterativo hasta la hoja más profunda). La recursión se dispara como máximo `O(log n)` niveles profundos, ya que en cada nivel se descarta al menos la mitad del árbol (el subárbol cuya altura sea igual confirma ser un árbol perfecto y se resuelve en `O(1)` con la fórmula). El producto de ambos factores da `O(log n × log n) = O(log²n)`, significativamente mejor que el `O(n)` de la fuerza bruta.

- **Espacio: `O(log n)`** — La única estructura auxiliar es el **call stack de la recursión**. En el peor caso, la pila de llamadas crece hasta la profundidad del árbol, que en un árbol binario completo es exactamente `O(log n)`. No se crean arrays, maps ni ninguna estructura de datos adicional en el heap.

---

## Intuición y Enfoque

La técnica utilizada es **Binary Lifting sobre la propiedad de Árbol Binario Completo**, combinada con **Bit Shifting** para el cálculo de nodos en tiempo constante.

**Premisa clave:** Un árbol binario completo (*complete binary tree*) es aquel donde todos los niveles están completamente llenos, excepto posiblemente el último, el cual se rellena de izquierda a derecha. Esta definición implica que:
- Si `leftHeight === rightHeight`, el árbol **es perfecto** (todos los niveles, incluido el último, están llenos). El número de nodos es exactamente `2^h - 1`, calculable en `O(1)`.
- Si `leftHeight > rightHeight`, el último nivel no está completo. La solución **no puede simplificarse** directamente, y se recurre en ambos subárboles.

**Lógica central:**
```
countNodes(root):
  1. Calcular leftHeight  → descender siempre por la izquierda.
  2. Calcular rightHeight → descender siempre por la derecha.
  3. Si son iguales → árbol perfecto: retornar (1 << h) - 1  [O(1)]
  4. Si difieren   → retornar 1 + countNodes(left) + countNodes(right)
```

**El operador Bitwise `<<` en el paso 3:** `1 << h` equivale a `2^h`. Para un árbol perfecto de altura `h`, el número de nodos es `2^h - 1`. Usar el desplazamiento de bits en lugar de `Math.pow` o `**` es la forma idiomática y más eficiente (operación de nivel de hardware).

**¿Por qué es óptima frente a la fuerza bruta?** Un DFS/BFS completo visitaría los `n` nodos para contarlos en `O(n)`. Esta solución explota la **propiedad estructural del árbol completo** para "cortocircuitar" subárboles enteros que son perfectos, resolviendo su conteo en `O(1)` en lugar de descender nodo por nodo. Es una aplicación directa del principio *"divide y vencerás con poda por propiedad estructural"*.

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** Los helpers `getLeftHeight` y `getRightHeight` se definen como **arrow functions** (`const fn = (node) => {}`) en el scope del módulo, fuera de `countNodes`. Esto es posible gracias al estilo funcional de JS y al hoisting semántico de `const`, aunque las arrow functions no se elevan, por lo que se declaran *después* de `countNodes` — algo que es válido en este contexto porque `countNodes` es llamada en tiempo de ejecución, no de parsing. El operador `<<` actúa sobre enteros de 32 bits con signo en JS; para alturas hasta 30 (el límite del problema), esto es seguro sin necesidad de `BigInt`.

- **PHP:** Los helpers se declaran como métodos de instancia `private`, siguiendo la orientación a objetos que impone la clase `Solution` de LeetCode. Se acceden mediante `$this->getLeftHeight($root)`, lo que es el equivalente directo de `getLeftHeight(root)` en JS. El operador `<<` en PHP también opera sobre enteros nativos; en sistemas de 64 bits (la mayoría de entornos PHP modernos), el rango es más que suficiente para los valores del problema. La comparación de nulidad usa `=== null` explícito, más estricto y correcto que el `!$node` de PHP, que podría ser falsy por otras razones.

> [!NOTE]
> Una diferencia arquitectónica relevante: en JS, los helpers son funciones libres del módulo (sin `this`), mientras que en PHP son métodos privados de la clase. Ambos logran el mismo encapsulamiento lógico, pero el modelo de PHP impone la clase como unidad de organización obligatoria para el juez de LeetCode.

---

## Lecciones Clave

- **Exploit Structural Properties (Poda por Invariante):** Antes de resolver un problema con fuerza bruta, identificar si la estructura de datos tiene **invariantes conocidas** (ej. "árbol completo", "arreglo ordenado", "heap válido") que permitan resolver subproblemas en `O(1)` en lugar de explorarlos. Este patrón transforma complejidades `O(n)` en `O(log²n)` o `O(log n)` y es fundamental en problemas de árboles, búsqueda binaria y estructuras de datos avanzadas.

- **Bit Shifting como reemplazo de potencias de 2:** El idioma `1 << h` para calcular `2^h` debe ser la herramienta predeterminada en cualquier algoritmo que opere sobre árboles binarios perfectos, segmentos de Segment Tree, o particiones tipo divide-y-vencerás. Es una operación de nivel de hardware (`O(1)` real), más legible en contexto algorítmico que `Math.pow(2, h)` o `2 ** h`, y señaliza inmediatamente al lector que se está trabajando con potencias de 2.
