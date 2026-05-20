# Merge Two Sorted Lists

## Análisis de Complejidad

- **Tiempo**: $O(m + n)$ donde $m$ y $n$ son las longitudes de `list1` y `list2` respectivamente -> El bucle `while` procesa exactamente un nodo por iteración, alternando entre ambas listas según el resultado de la comparación de valores. Cada nodo de ambas listas es visitado y enlazado exactamente **una vez**. La operación de cola (`current.next = list1 || list2`) adjunta los nodos restantes de la lista no agotada en $O(1)$ mediante un simple redireccionamiento de puntero, sin iterar sobre ellos. La complejidad temporal total es proporcional a la suma de las longitudes de ambas listas.
- **Espacio**: $O(1)$ -> La fusión se realiza **in-place** reutilizando los nodos existentes de ambas listas: no se crean nodos nuevos (excepto el nodo `dummy` centinela de coste fijo), no se asignan arreglos ni se utiliza recursión. Solo se mantienen dos variables de puntero auxiliares (`dummy` y `current`). La lista resultante está formada enteramente por los nodos originales de `list1` y `list2`, cuyos punteros `next` son simplemente reenlazados.

## Intuición y Enfoque

El problema solicita fusionar dos listas enlazadas ya ordenadas en una única lista ordenada, manteniendo el orden ascendente. La restricción implícita es no crear nodos nuevos innecesariamente (se reutilizan los existentes).

Un enfoque de fuerza bruta podría extraer todos los valores a un arreglo, ordenar el arreglo, y construir una lista nueva. Esto tomaría $O((m + n) \log(m + n))$ de tiempo y $O(m + n)$ de espacio, ignorando la estructura ya ordenada de las listas de entrada.

La solución implementa el **Patrón de Nodo Centinela (Dummy Head) con Fusión Iterativa por Comparación**. Esta técnica explota la propiedad de que ambas listas ya están ordenadas para realizar la fusión en una sola pasada de $O(m + n)$:

1. **Nodo Dummy (Centinela)**: Se crea un nodo ficticio `dummy` con valor `0` que actúa como la cabeza ficticia de la lista resultante. Su propósito es eliminar el caso especial de "inicialización de la cabeza de la lista resultado", ya que siempre existe un nodo previo al primer nodo real al cual enlazar. Al finalizar, `dummy.next` apunta al primer nodo real de la lista fusionada.

2. **Puntero de Construcción (`current`)**: Mantiene la referencia al último nodo ya enlazado en la lista resultado, actuando como el "extremo activo" de la cadena en construcción.

3. **Ciclo de Comparación Voraz (Greedy)**: En cada iteración, se comparan los valores del nodo frontal de ambas listas. El nodo con el valor menor es seleccionado (estrategia greedy óptima para mantener el orden), enlazado al extremo de la lista resultado (`current.next = nodoMenor`), y su lista de origen avanza al siguiente nodo. El puntero `current` también avanza.

4. **Adjunción de Cola**: Cuando una lista se agota, la otra puede contener nodos restantes que ya están ordenados entre sí. En lugar de iterar sobre ellos uno a uno, se adjunta directamente el puntero a la cabeza del remanente con `current.next = list1 || list2`, operación de coste $O(1)$.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Condición del Bucle con Evaluación Truthy**: `while (list1 && list2)` aprovecha la falsyness de `null` en JavaScript. El bucle continúa mientras ambos punteros apunten a un nodo válido (truthy), y termina en el instante en que cualquiera de los dos se agota (se convierte en `null`).
  - **Adjunción de Cola con `||`**: La línea `current.next = list1 || list2` es una expresión compacta y elegante del operador lógico OR de cortocircuito. Si `list1` es truthy (no nulo), asigna `list1`; de lo contrario, asigna `list2`. Dado que el bucle termina cuando al menos uno de los dos es `null`, esta línea garantiza que se adjunte el remanente de la lista que aún tiene nodos, o `null` si ambas se agotaron simultáneamente.
  - **Instanciación de `ListNode`**: La llamada `new ListNode(0)` para crear el nodo dummy utiliza el constructor de función de JavaScript, donde `next` toma el valor `null` por defecto según la definición: `(next===undefined ? null : next)`.

- **PHP**:
  - **Operador Elvis (`?:`) para la Cola**: La línea `$current->next = $list1 ?: $list2;` utiliza el **operador Elvis** (forma corta del operador ternario de PHP), que es funcionalmente equivalente a `$list1 ? $list1 : $list2`. Si `$list1` es truthy (no nulo), se asigna `$list1`; si es falsy (nulo), se asigna `$list2`. Es el equivalente idiomático del `list1 || list2` de JavaScript, con una semántica muy similar pero con la sintaxis propia de PHP.
  - **Condición del Bucle Truthy-Compatible**: `while ($list1 && $list2)` funciona en PHP gracias a que un objeto `ListNode` evalúa como truthy y `null` evalúa como falsy, exactamente igual que en JavaScript para este caso específico.
  - **Acceso a Miembros con `->`**: PHP utiliza el operador `->` para acceder a las propiedades `val` y `next` de los objetos `ListNode`, en contraste con el operador `.` de JavaScript. La estructura lógica y el flujo de control son completamente isomorfos entre ambas implementaciones.
  - **Constructor con Parámetros por Defecto**: En la definición de `ListNode`, PHP permite `function __construct($val = 0, $next = null)`, haciendo que `new ListNode(0)` sea una instanciación limpia del nodo dummy con `next` en `null` por defecto.

## Lecciones Clave

- **El Patrón del Nodo Dummy (Centinela) en Listas Enlazadas**: El uso de un nodo ficticio `dummy` como cabeza provisional es uno de los patrones de diseño más importantes en la manipulación de listas enlazadas. Elimina los casos especiales de la inicialización de la cabeza de la lista resultante, simplificando el código y evitando condiciones adicionales. Este patrón se aplica directamente en _Merge K Sorted Lists_, _Add Two Numbers_, _Reverse Linked List II_, y cualquier problema donde se construya una nueva cadena de nodos dinámicamente.
- **Fusión Greedy como Paradigma de Construcción Ordenada**: La estrategia de seleccionar siempre el menor elemento disponible en cada paso es la base del algoritmo Merge Sort (específicamente su fase de fusión) y de las colas de prioridad. Internalizar que listas previamente ordenadas pueden fusionarse en $O(m + n)$ sin reordenamiento adicional es fundamental para entender el análisis de complejidad de algoritmos de ordenación por comparación y para diseñar sistemas de fusión de flujos de datos (_data stream merging_) en arquitecturas de procesamiento distribuido.
