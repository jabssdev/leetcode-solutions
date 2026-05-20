# Reverse Linked List

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos en la lista -> El bucle `while` itera exactamente una vez sobre cada nodo de la lista. En cada iteración se ejecutan cuatro operaciones atómicas de reasignación de punteros, todas en tiempo constante $O(1)$. No hay bucles anidados, llamadas recursivas ni estructuras de búsqueda. La complejidad temporal es estrictamente lineal.
- **Espacio**: $O(1)$ -> La inversión se realiza **in-place** reutilizando los nodos existentes y únicamente redirigiendo sus punteros `next`. Solo se emplean tres variables de puntero auxiliares: `prev`, `current` y `nextTemp`. No se crean nuevos nodos, arreglos, pilas ni se recurre a recursión que pueda incrementar el Call Stack. El consumo de memoria es constante independientemente de la longitud de la lista.

## Intuición y Enfoque

El problema solicita invertir el orden de una lista enlazada simple _in-place_, convirtiendo la cabeza original en la cola y la cola en la nueva cabeza, sin crear una nueva lista ni invertir los valores, sino redirigiendo los punteros `next` de cada nodo.

La dificultad inherente de las listas enlazadas simples es que los nodos solo conocen a su **sucesor** (`next`), no a su **predecesor**. Para invertir un nodo, necesitamos hacer que su `next` apunte hacia atrás (al nodo anterior), pero ese nodo anterior ya fue procesado. La solución requiere mantener una "memoria" del nodo previo y del siguiente aún por procesar simultáneamente.

La solución implementa el algoritmo de **Inversión Iterativa con Tres Punteros (Three-Pointer Iterative Reversal)**:

1. **Inicialización**: `prev = null` (el primer nodo, al invertirse, debe apuntar a `null` ya que se convierte en la nueva cola) y `current = head` (comienza en la cabeza de la lista).

2. **Ciclo de inversión**: En cada iteración se ejecutan cuatro pasos **en un orden crítico e irremplazable**:
   - **`nextTemp = current.next`** — Se guarda la referencia al siguiente nodo **antes** de sobrescribir el puntero. Sin este paso, se perdería el resto de la lista al redirigir `current.next`.
   - **`current.next = prev`** — Se **invierte** el puntero del nodo actual: en lugar de apuntar hacia adelante, ahora apunta al nodo previo (hacia atrás). Esta es la operación de inversión propiamente dicha.
   - **`prev = current`** — El nodo actual pasa a ser el "previo" para el siguiente nodo de la cadena.
   - **`current = nextTemp`** — Se avanza al siguiente nodo usando el puntero guardado en el primer paso.

3. **Terminación**: Cuando `current` es `null`, todos los nodos han sido procesados. En este punto, `prev` apunta al último nodo procesado, que es la nueva cabeza de la lista invertida, y se retorna.

La secuencia de los cuatro pasos es una invariante rígida del algoritmo: alterar el orden produce una pérdida irreversible de referencias o una reversión incorrecta.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Condición del Bucle con Evaluación Truthy**: `while (current)` aprovecha la falsyness de `null` en JavaScript. El bucle continúa mientras `current` sea un objeto `ListNode` truthy y termina cuando `current` se vuelve `null` (falsy). Esto produce una sintaxis más concisa que una comparación explícita.
  - **Declaración `const` para `nextTemp`**: Se utiliza `const nextTemp = current.next` dentro del bucle, declarando la variable como constante de bloque. Esto comunica explícitamente que `nextTemp` no cambiará dentro de la iteración actual y que su ámbito se limita a dicha iteración. En cada nueva iteración, una nueva instancia de `nextTemp` es creada y destruida.
  - **Inicialización de `prev` con `null`**: La inicialización `let prev = null` es semánticamente precisa: el nodo que actualmente es la cabeza (y se convertirá en la cola tras la inversión) debe apuntar a `null` como su nuevo `next`.

- **PHP**:
  - **Condición del Bucle con Comparación Explícita**: PHP utiliza `while ($current !== null)` con comparación estricta contra `null`. Aunque un objeto `ListNode` es truthy en PHP (similar a JavaScript), la comparación explícita es la práctica idiomática preferida en PHP para variables de tipo objeto/null, ya que comunica con precisión la condición de parada y previene comportamientos inesperados en contextos donde el objeto pudiera tener propiedades falsy.
  - **Tipado Implícito de `$nextTemp`**: PHP no tiene `const` a nivel de variable local (solo `define()` o `const` a nivel de clase/namespace). La variable `$nextTemp` es simplemente reasignada en cada iteración del bucle. El motor Zend gestiona el ciclo de vida de la referencia automáticamente mediante su sistema de conteo de referencias (_reference counting_).
  - **Isomorfismo Algorítmico Total**: La secuencia de cuatro pasos (`$nextTemp = $current->next`, `$current->next = $prev`, `$prev = $current`, `$current = $nextTemp`) es estructuralmente idéntica a la versión de JavaScript, con las únicas diferencias siendo la sintaxis de acceso a miembros (`->` vs `.`), el prefijo `$`, y la condición de bucle. El algoritmo es completamente portable entre ambos lenguajes.

## Lecciones Clave

- **El Orden de Reasignación de Punteros como Invariante Crítica**: Este ejercicio refuerza una lección fundamental de la programación con estructuras de datos enlazadas: en cualquier operación que requiera redirigir múltiples punteros simultáneamente, el **orden exacto de las reasignaciones** es un invariante del algoritmo. Guardar referencias antes de sobrescribir (`nextTemp = current.next` antes de `current.next = prev`) es el principio que previene la pérdida irreversible de partes de la estructura. Este mismo principio aplica en inversión de sublistas (_Reverse Linked List II_), rotación de listas, y cualquier operación de rewiring de nodos.
- **La Inversión Iterativa como Base de Operaciones Complejas en Listas**: Dominar la inversión iterativa con tres punteros es un prerequisito para resolver problemas avanzados como _Reverse Nodes in k-Group_, _Palindrome Linked List_ (que requiere invertir la segunda mitad), _Reorder List_, y variantes de inversión parcial. La intuición de mantener `prev` como el "nodo ya procesado" y `current` como el "nodo en proceso" es una plantilla reutilizable para toda una familia de manipulaciones de listas enlazadas.
