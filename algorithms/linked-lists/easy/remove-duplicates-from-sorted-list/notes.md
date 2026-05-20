# Remove Duplicates from Sorted List

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos en la lista -> El puntero `current` recorre la lista exactamente una vez de principio a fin. Cuando detecta un duplicado, el salto de puntero `current.next = current.next.next` es una operación de tiempo constante $O(1)$ que no desplaza a `current` hacia adelante; el siguiente duplicado consecutivo será evaluado en la siguiente iteración del mismo nodo. Cuando no hay duplicado, `current` avanza. En total, cada nodo es visitado como máximo **dos veces**: una vez cuando es `current` y otra cuando es evaluado como `current.next`. La complejidad temporal es estrictamente lineal.
- **Espacio**: $O(1)$ -> La eliminación se realiza **in-place** manipulando únicamente los punteros `next` de los nodos existentes. Solo se emplea una variable de puntero auxiliar (`current`). No se crean nodos nuevos, arreglos temporales, conjuntos de valores vistos ni se recurre a recursión. La lista resultante reutiliza los nodos originales con sus enlaces modificados.

## Intuición y Enfoque

El problema solicita eliminar todos los nodos duplicados de una lista enlazada **ya ordenada**, dejando solo la primera ocurrencia de cada valor distinto.

La precondición de ordenamiento es la clave fundamental: al estar la lista ordenada, todos los nodos con el mismo valor están necesariamente **contiguos**. Esto significa que si el nodo actual tiene el mismo valor que el siguiente, ese siguiente es un duplicado que puede eliminarse directamente. Este razonamiento es completamente análogo al problema _Remove Duplicates from Sorted Array_ pero operando sobre la estructura de punteros de una lista enlazada en lugar de índices de un arreglo.

La solución implementa un enfoque de **Puntero de Recorrido Único (Single Pointer Traversal)** con lógica de "saltar duplicados":

1. Se inicializa el puntero `current` en la cabeza de la lista.
2. En cada iteración, se compara el valor del nodo actual (`current.val`) con el valor del siguiente nodo (`current.next.val`).
3. **Si son iguales**: el nodo siguiente es un duplicado. Se "salta" dicho nodo reasignando `current.next = current.next.next`, cortocircuitando el puntero sobre el duplicado. El nodo duplicado queda desreferenciado (sin conexión a la cadena) y `current` **no avanza**, porque el nuevo `current.next` podría ser otro duplicado del mismo valor que requiere evaluación inmediata.
4. **Si son distintos**: no hay duplicado en la posición actual. Se avanza `current = current.next` hacia el siguiente valor único.
5. El bucle termina cuando `current` o `current.next` es `null`, y se retorna `head` (que sigue apuntando al primer nodo de la lista ya depurada).

La elegancia del algoritmo reside en que no requiere un puntero "previo" explícito ni una lógica de reconexión compleja: `current` actúa simultáneamente como el nodo "guardián" del último valor único confirmado y como el ancla desde la cual se elimina el siguiente duplicado.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Sin Guarda Explícita para Lista Vacía**: La solución en JavaScript omite una verificación inicial para `head === null`. Esto es seguro porque la condición del bucle `while (current !== null && current.next !== null)` evalúa `current !== null` como primera subexpresión. Si `head` es `null`, `current` también lo es, la condición falla inmediatamente por cortocircuito, y la función retorna `head` (que es `null`), comportándose correctamente.
  - **Comparación Estricta (`===`)**: Se utiliza `current.val === current.next.val` para comparar valores con identidad de tipo. Aunque LeetCode garantiza enteros, el operador `===` previene comportamientos inesperados ante posibles coerciones de tipo por parte del motor JavaScript.
  - **Retorno de la Cabeza Original**: La función retorna `head` directamente, no `current`. Esto funciona porque `head` siempre apunta al primer nodo (que nunca se elimina; solo se eliminan nodos internos/cola), y la lista ha sido modificada _in-place_ a través de las reasignaciones de `current.next`.

- **PHP**:
  - **Guarda Defensiva Explícita**: La solución en PHP incluye una verificación explícita al inicio: `if ($head === null) return null;`. Aunque la condición del bucle también maneja este caso, la guarda es una práctica defensiva que comunica claramente la intención: una lista vacía es un caso base que se resuelve de inmediato, sin necesidad de inicializar el puntero `$current`.
  - **Comparación de Valores con `===`**: `$current->val === $current->next->val` usa el operador de identidad estricta. En PHP, esto es especialmente importante ya que los valores de los nodos podrían ser `0`, y el operador `==` podría generar falsos positivos al comparar con `null` (`0 == null` → `true` en PHP con coerción débil).
  - **Acceso Encadenado con `->`**: El acceso `$current->next->next` está protegido por la condición del bucle `$current->next !== null`, que garantiza que `$current->next` no es `null` antes de que se intente acceder a su propiedad `next`. Si `$current->next->next` fuera `null`, la asignación `$current->next = null` es válida y simplemente trunca la lista correctamente.
  - **Isomorfismo Estructural**: La lógica de la solución en PHP es un espejo casi perfecto de la versión en JavaScript, con las únicas diferencias siendo la guarda inicial explícita, el operador `->` para acceso a miembros, y el prefijo `$` en las variables.

## Lecciones Clave

- **Salto de Puntero como Técnica de Eliminación en Listas Enlazadas**: En listas enlazadas, "eliminar" un nodo no requiere liberación de memoria explícita (en lenguajes con garbage collection) ni desplazamiento de datos. Basta con realizar un **salto de puntero** (`current.next = current.next.next`) para desreferenciar el nodo objetivo de la cadena. Este patrón es la primitiva fundamental de eliminación en listas enlazadas y se aplica directamente en _Remove Linked List Elements_, _Remove N-th Node From End of List_, y _Remove Duplicates from Sorted List II_.
- **Aprovechamiento del Orden como Eliminador de Estado Auxiliar**: Al igual que en su variante de arreglos, este problema demuestra que el **ordenamiento previo** de una estructura de datos elimina la necesidad de rastrear elementos vistos mediante conjuntos o mapas ($O(N)$ de espacio). La contigüidad de duplicados en una secuencia ordenada transforma la búsqueda global en una comparación local entre vecinos inmediatos, reduciendo el coste espacial a $O(1)$. Siempre que la entrada esté ordenada, explotar la localidad de los duplicados debe ser la primera estrategia de diseño considerada.
