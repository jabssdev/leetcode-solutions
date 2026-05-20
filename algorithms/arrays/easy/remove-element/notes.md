# Remove Element

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud del arreglo `nums` -> El algoritmo realiza un único recorrido lineal completo sobre el arreglo mediante un bucle `for` que itera desde la posición `0` hasta `N - 1`. En cada iteración, se ejecuta una comparación de desigualdad y, condicionalmente, una asignación directa por índice y un incremento del contador, todas operaciones de tiempo constante $O(1)$. No existen bucles anidados, llamadas recursivas ni operaciones de desplazamiento de elementos, lo que garantiza una complejidad temporal estrictamente lineal.
- **Espacio**: $O(1)$ -> La eliminación se realiza completamente **in-place** sobre el arreglo original. El algoritmo solo emplea dos variables escalares de índice: `k` (puntero de escritura) e `i` (puntero de lectura). No se crean arreglos auxiliares, conjuntos, mapas ni se incurre en crecimiento del Call Stack, asegurando un consumo de memoria constante independiente del tamaño de la entrada.

## Intuición y Enfoque

El problema solicita eliminar todas las ocurrencias de un valor específico `val` de un arreglo **no ordenado** y retornar la cantidad $k$ de elementos restantes. La operación debe realizarse *in-place*, dejando los primeros $k$ elementos del arreglo con los valores que no fueron eliminados (el orden relativo puede o no preservarse).

Un enfoque de fuerza bruta implicaría, por cada ocurrencia encontrada, desplazar todos los elementos subsiguientes una posición hacia la izquierda para cerrar el hueco, resultando en una complejidad de $O(N^2)$ en el peor de los casos. Este enfoque es innecesariamente costoso.

La solución implementa la técnica de **Dos Punteros con Roles Asimétricos (Two Pointers: Lectura/Escritura)**, también conocida como el patrón de **compactación in-place**:

1. **Puntero de Lectura (`i`)** — Recorre linealmente cada elemento del arreglo de izquierda a derecha sin excepción, actuando como el explorador del flujo de datos.
2. **Puntero de Escritura (`k`)** — Avanza únicamente cuando se encuentra un elemento que **no** debe ser eliminado. Marca la siguiente posición disponible en el subarreglo compactado de elementos válidos.
3. En cada iteración, si `nums[i] !== val`, el elemento es "retenido": se copia a la posición `nums[k]` y el puntero de escritura `k` avanza. Si `nums[i] === val`, el puntero `i` avanza pero `k` permanece fijo, efectivamente "saltando" el elemento a eliminar.
4. Al finalizar el recorrido, `k` representa exactamente la cantidad de elementos válidos, y las primeras $k$ posiciones del arreglo contienen exclusivamente los elementos retenidos en su orden relativo original.

La elegancia de este enfoque radica en que no necesita detectar, marcar ni desplazar elementos: simplemente **sobrescribe desde el frente**, dejando que los elementos no deseados queden naturalmente fuera del rango útil $[0, k)$.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Comparación Estricta (`!==`)**: Se utiliza el operador de desigualdad estricta `nums[i] !== val` para evitar cualquier coerción implícita de tipos por parte del motor V8. Esto garantiza que la comparación sea exclusivamente por valor e identidad de tipo, eliminando falsos positivos (por ejemplo, `0 !== "0"` es `true`, mientras que `0 != "0"` sería `false`).
  - **Mutación Nativa por Referencia**: Al ser los arreglos objetos en JavaScript, la asignación `nums[k] = nums[i]` modifica directamente la instancia del arreglo en el ámbito del llamador sin necesidad de anotaciones especiales en la firma de la función.
  - **Acceso Directo a `.length`**: La propiedad `nums.length` se evalúa en cada iteración del bucle `for`. En la práctica, los motores modernos (V8, SpiderMonkey) optimizan este acceso a una lectura de campo constante $O(1)$ cuando el arreglo no cambia de tamaño dentro del bucle, por lo que no genera penalización de rendimiento.

- **PHP**:
  - **Paso por Referencia Explícito (`&$nums`)**: PHP emplea semántica de *copy-on-write* para arreglos. Sin el operador `&`, la primera escritura a `$nums[$k]` dispararía una copia completa del arreglo en memoria, duplicando el consumo de espacio a $O(N)$ y violando la restricción *in-place*. El uso de `&$nums` es obligatorio para que las mutaciones se reflejen directamente en el arreglo original del llamador.
  - **Precálculo de Longitud**: Se almacena `count($nums)` en la variable `$length` antes del bucle. Aunque `count()` es $O(1)$ en PHP (la longitud se almacena como metadato en la estructura `zend_array`), esta práctica es idiomática y comunica explícitamente que la longitud del arreglo es un valor fijo durante la ejecución del algoritmo, favoreciendo la legibilidad y la intención del código.
  - **Comparación Estricta en PHP (`!==`)**: Al igual que en JS, se emplea `$nums[$i] !== $val` para evitar la coerción débil histórica de PHP. Esto es crítico en PHP ya que comparaciones como `0 == false` o `0 == null` evalúan como `true` con el operador no estricto `==`, lo cual podría introducir errores sutiles si `$val` fuera `0`.

## Lecciones Clave

- **El Patrón de Compactación In-Place (Two Pointers: Lectura/Escritura)**: Este ejercicio refuerza el patrón más fundamental de manipulación de arreglos *in-place*. La misma estructura algorítmica exacta (un puntero rápido de lectura y uno lento de escritura con una condición de filtrado) se aplica directamente a problemas como *Remove Duplicates from Sorted Array*, *Move Zeroes*, y cualquier escenario donde necesitemos particionar o filtrar elementos de una colección sin memoria auxiliar.
- **Sobrescritura frente a Desplazamiento**: Una lección crítica de ingeniería de software es que eliminar elementos de un arreglo no requiere "mover" o "desplazar" nada. Basta con **copiar los elementos válidos hacia el frente** y redefinir el límite lógico del arreglo. Este principio de "escritura selectiva" es análogo al patrón de *stream processing* en sistemas de producción, donde los datos se filtran y reescriben en un buffer sin necesidad de operaciones destructivas costosas.
