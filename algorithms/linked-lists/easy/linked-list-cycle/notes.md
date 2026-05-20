# Linked List Cycle

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de nodos en la lista -> En el caso de que no haya ciclo, el puntero `fast` recorre la lista completa en $\lceil N/2 \rceil$ iteraciones y el bucle termina al alcanzar `null`. En el caso de que haya ciclo, se puede demostrar matemáticamente que el puntero `fast` alcanza al puntero `slow` dentro del ciclo en como máximo $\lambda$ iteraciones adicionales (donde $\lambda$ es la longitud del ciclo), lo que resulta en un total de a lo sumo $N$ iteraciones. Cada iteración ejecuta operaciones de avance de puntero y comparación de identidad en tiempo constante $O(1)$.
- **Espacio**: $O(1)$ -> El algoritmo utiliza únicamente dos variables de puntero (`slow` y `fast`). No se almacenan nodos en conjuntos, mapas, arreglos ni ninguna otra estructura auxiliar de tamaño variable. No hay recursión que incremente el Call Stack. El consumo de memoria es constante independientemente del tamaño o topología de la lista.

## Intuición y Enfoque

El problema solicita detectar si una lista enlazada contiene un ciclo (un nodo cuyo puntero `next` apunta a un nodo anterior en la lista, formando un bucle infinito).

Un enfoque con Hash Set almacenaría cada nodo visitado y verificaría si el nodo actual ya fue visto, logrando $O(N)$ de tiempo pero con $O(N)$ de espacio. Esto es correcto pero subóptimo en memoria.

La solución implementa el **Algoritmo de Detección de Ciclos de Floyd (Floyd's Cycle Detection Algorithm)**, también conocido popularmente como el algoritmo de la **Tortuga y la Liebre** (_Tortoise and Hare_). Es la solución canónica para este problema con óptimos absolutos de $O(N)$ tiempo y $O(1)$ espacio.

La intuición se basa en un razonamiento físico directo: si dos corredores avanzan por una pista **circular** (con ciclo), el corredor más rápido inevitablemente alcanzará y superará al corredor más lento, y en algún momento ambos se encontrarán en el mismo punto. Si la pista es **lineal** (sin ciclo), el corredor más rápido simplemente llegará al final primero sin que ocurra ningún encuentro.

La demostración formal es la siguiente: sea $\mu$ el índice del nodo donde comienza el ciclo y $\lambda$ la longitud del ciclo. Cuando `slow` entra al ciclo (tras $\mu$ pasos), `fast` lleva $2\mu$ pasos recorridos. La diferencia entre los punteros dentro del ciclo es $\mu \mod \lambda$. Como `fast` avanza un paso por iteración más que `slow` (dentro del ciclo), la distancia entre ellos disminuye en 1 por cada iteración, garantizando la colisión en a lo sumo $\lambda$ iteraciones adicionales.

El flujo del algoritmo:

1. Se inicializan `slow` y `fast` en la cabeza de la lista.
2. En cada iteración, `slow` avanza **un** nodo (`slow = slow.next`) y `fast` avanza **dos** nodos (`fast = fast.next.next`).
3. Si en algún punto `slow === fast` (misma referencia de objeto en memoria), se detectó un ciclo y se retorna `true`.
4. Si `fast` o `fast.next` llegan a `null`, la lista es acíclica y se retorna `false`.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Guarda con Evaluación Truthy**: La validación inicial `if (!head || !head.next) return false;` explota la falsyness de `null` en JavaScript para una sintaxis compacta. Verifica en una sola línea si la lista está vacía o tiene un único nodo (ambos casos acíclicos por definición).
  - **Condición del Bucle con Evaluación Truthy**: `while (fast && fast.next)` aprovecha que un objeto `ListNode` siempre es truthy, mientras que `null` es falsy. Esto garantiza que `fast.next.next` nunca arroje un error de acceso a propiedad de `null` ya que `fast.next` está verificado antes de acceder a su `.next`.
  - **Comparación de Identidad por Referencia (`===`)**: La condición `slow === fast` compara si ambos punteros apuntan al **mismo nodo en memoria**, no si tienen el mismo valor. Esta es la semántica correcta para detectar que los dos punteros han convergido en el mismo objeto de la lista enlazada.

- **PHP**:
  - **Comparaciones Explícitas contra `null`**: La guarda inicial `if ($head === null || $head->next === null)` y la condición del bucle `while ($fast !== null && $fast->next !== null)` utilizan comparaciones explícitas con el operador de identidad `===`. En PHP, la coerción débil del operador `==` podría generar comportamientos inesperados con objetos que implementen `__toString` o tengan propiedades con valores falsy, por lo que `===` es la práctica correcta y defensiva.
  - **Comparación de Identidad de Objetos (`===`)**: En PHP, el operador `===` sobre objetos verifica que sean la **misma instancia** (mismo identificador de objeto en el heap de Zend), lo que corresponde exactamente a la comparación por referencia que necesitamos para confirmar que `$slow` y `$fast` apuntan al mismo nodo.
  - **Acceso Encadenado con `->next->next`**: PHP permite el encadenamiento de acceso a miembros `$fast->next->next` de forma nativa. Este acceso está protegido por la condición del bucle `$fast->next !== null`, que garantiza que `$fast->next` no es `null` antes de acceder a `$fast->next->next`.
  - **Sin Acceso a `$head->next` Seguro**: La guarda en PHP accede a `$head->next` solo después de verificar que `$head !== null`, siguiendo un orden lógico de verificación que previene errores fatales por acceso a miembro de objeto nulo.

## Lecciones Clave

- **El Algoritmo de Floyd como Detector Universal de Ciclos**: El algoritmo de la Tortuga y la Liebre es una de las técnicas más universales de la teoría de algoritmos y trasciende las listas enlazadas. Se aplica directamente en detección de ciclos en grafos dirigidos, detección de secuencias periódicas en generadores de números pseudoaleatorios, encontrar el punto de entrada de un ciclo (_Linked List Cycle II_), y en algoritmos de criptografía (factorización de Pollard). Es una herramienta que todo ingeniero de software debe tener interiorizada.
- **Velocidad Diferencial como Mecanismo de Detección de Estructuras**: Este ejercicio enseña un principio poderoso y extensible: cuando dos exploradores de una misma estructura avanzan a velocidades diferentes, sus comportamientos relativos revelan propiedades estructurales profundas. Si convergen: la estructura tiene periodicidad o ciclo. Si no convergen: la estructura es finita y acíclica. Este mismo principio de "velocidades diferenciales" se recicla en encontrar el nodo medio de una lista (_Middle of the Linked List_), el k-ésimo nodo desde el final (_Remove N-th Node From End of List_), y en el problema del punto de inicio del ciclo.
