# Remove Linked List Elements

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos en la lista -> El puntero `current` parte desde el nodo dummy y recorre la lista exactamente una vez hasta que `current.next` se convierte en `null`. En cada iteración, ya sea se realiza un salto de puntero (eliminación en $O(1)$) o se avanza `current` (en $O(1)$). No hay bucles anidados ni recursión, garantizando una complejidad temporal lineal.
- **Espacio**: $O(1)$ -> La eliminación se realiza **in-place** manipulando únicamente los punteros `next` de los nodos existentes. La única estructura creada es el nodo `dummy` centinela, cuyo coste es fijo e independiente del tamaño de la entrada. Solo se emplea una variable de puntero adicional (`current`). No se crean arreglos, conjuntos ni se recurre a recursión.

## Intuición y Enfoque

El problema solicita eliminar todos los nodos de una lista enlazada cuyo valor sea igual a `val`, retornando la cabeza de la lista resultante. La lista puede no estar ordenada y los nodos objetivo pueden aparecer en cualquier posición, incluyendo la cabeza.

La complejidad inherente del problema es la eliminación de la **cabeza** de la lista: si los primeros nodos contienen el valor a eliminar, la nueva cabeza de la lista cambia. Sin un nodo ficticio, se requeriría un tratamiento especial previo al bucle principal para encontrar la nueva cabeza válida, lo que introduce código redundante y propenso a errores.

La solución implementa el **Patrón de Nodo Dummy (Centinela) con Salto de Puntero por Anticipación (Look-Ahead Pointer)**:

1. **Nodo Dummy**: Se crea un nodo ficticio `new ListNode(0, head)` cuyo `next` apunta directamente a la cabeza original de la lista. El `current` comienza en este nodo dummy, no en `head`. Esto garantiza que **siempre existe un nodo previo** a cualquier nodo que deba evaluarse o eliminarse, incluyendo la cabeza original. La eliminación de la cabeza se convierte en un caso ordinario: simplemente `dummy.next = head.next`.

2. **Inspección por Anticipación (Look-Ahead)**: El bucle evalúa `current.next` (el nodo _siguiente_ al actual) en lugar del nodo `current` mismo. Este enfoque de "mirar hacia adelante" es la técnica canónica para la eliminación de nodos en listas enlazadas simples, ya que para desconectar un nodo de la cadena necesitamos acceso al nodo **anterior**, que en este caso es siempre `current`.

3. **Lógica de Filtrado**:
   - Si `current.next.val === val`: el siguiente nodo debe eliminarse. Se ejecuta el salto `current.next = current.next.next`, desreferenciando el nodo objetivo. `current` **no avanza**, ya que el nuevo `current.next` podría también tener el valor objetivo.
   - Si `current.next.val !== val`: el siguiente nodo es válido. Se avanza `current = current.next`.

4. Al finalizar, `dummy.next` apunta al primer nodo sobreviviente de la lista depurada (o a `null` si todos los nodos fueron eliminados).

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Inicialización del Dummy con `next` Preenlazado**: Se usa `new ListNode(0, head)` pasando `head` directamente como segundo argumento al constructor. Esto preenlaza el dummy a la lista original en el momento de su creación, evitando una asignación manual `dummy.next = head` posterior. Esta es la forma idiomática con el constructor de `ListNode` de LeetCode que acepta `(val, next)`.
  - **Condición del Bucle con Evaluación Truthy**: `while (current.next)` aprovecha que `null` es falsy en JavaScript. El bucle termina cuando `current.next` es `null` (fin de la lista), sin necesidad de comparación explícita. Dentro del bucle, `current.next` siempre es un objeto válido (truthy), por lo que el acceso a `current.next.val` y `current.next.next` es seguro.
  - **Comparación Estricta (`===`)**: Se usa `current.next.val === val` para comparar el valor del nodo con el valor objetivo, evitando coerciones de tipo no deseadas (por ejemplo, `0 == false` o `1 == "1"` en comparaciones débiles).

- **PHP**:
  - **Condición del Bucle con Comparación Explícita**: PHP utiliza `while ($current->next !== null)` con comparación explícita contra `null`, en lugar de la evaluación truthy implícita de JavaScript. Esta es la práctica idiomática y segura en PHP: un objeto `ListNode` es truthy, pero la verificación explícita con `!== null` comunica con mayor precisión la intención semántica del código y previene comportamientos inesperados con objetos que pudieran tener propiedades mágicas o implementaciones de `__toString`.
  - **Isomorfismo Estructural con JS**: La lógica del algoritmo es un espejo casi perfecto de la versión en JavaScript. Ambas usan el mismo patrón de nodo dummy preenlazado (`new ListNode(0, $head)`), el mismo bucle de inspección por anticipación, la misma estructura `if/else` de salto o avance, y el mismo retorno de `dummy->next`. Las diferencias se reducen a sintaxis: `->` vs `.` para acceso a propiedades, `$` en las variables, y la comparación explícita del bucle.
  - **Comparación Estricta (`===`)**: Al igual que en JS, se usa `$current->next->val === $val` para garantizar una comparación de valor y tipo, evitando la coerción débil histórica de PHP donde `0 == false` o `0 == null` evaluarían como `true`.

## Lecciones Clave

- **El Nodo Dummy como Igualador de Casos**: Este problema ilustra con claridad el **valor del nodo centinela** en listas enlazadas. Sin él, la eliminación de nodos en la cabeza de la lista requeriría un bucle o condicional separado para encontrar la nueva cabeza válida antes del bucle principal, duplicando la lógica. El nodo dummy unifica el tratamiento de todos los nodos (incluyendo la cabeza) en un único flujo de control elegante. Es el patrón preferido para cualquier problema de eliminación selectiva en listas enlazadas donde la cabeza puede verse afectada.
- **Inspección por Anticipación como Primitiva de Eliminación Segura**: En listas enlazadas simples (sin puntero previo), para eliminar el nodo `X` necesitamos acceso al nodo que precede a `X`. La técnica de "Look-Ahead" —donde el bucle siempre evalúa `current.next` en lugar de `current`— garantiza que `current` sea siempre el nodo previo al nodo bajo evaluación, habilitando el salto de puntero directo. Esta primitiva se reutiliza en _Remove Duplicates from Sorted List_, _Remove N-th Node From End of List_, y cualquier problema donde se deba desconectar selectivamente nodos de una cadena enlazada.
