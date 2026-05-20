# Remove Duplicates from Sorted Array

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud del arreglo `nums` -> El algoritmo realiza un único recorrido lineal sobre el arreglo mediante un bucle `for` que itera desde la posición `1` hasta `N - 1`. En cada iteración, se ejecuta una comparación de desigualdad y, condicionalmente, una asignación y un incremento, todas operaciones de tiempo constante $O(1)$. No hay bucles anidados ni llamadas recursivas, por lo que la complejidad temporal es estrictamente lineal.
- **Espacio**: $O(1)$ -> La eliminación de duplicados se realiza completamente **in-place** sobre el arreglo original. El algoritmo únicamente requiere dos variables escalares de índice (`k` e `i`) para controlar el puntero de escritura y el puntero de lectura respectivamente. No se crean arreglos auxiliares, conjuntos, mapas ni se incurre en crecimiento del Call Stack, garantizando un consumo de memoria constante independiente del tamaño de la entrada.

## Intuición y Enfoque

El problema solicita eliminar duplicados de un arreglo **ya ordenado de forma no decreciente** y retornar la cantidad de elementos únicos $k$. La restricción clave es que la operación debe hacerse _in-place_, modificando las primeras $k$ posiciones del arreglo original con los valores únicos.

La precondición de ordenamiento es fundamental: todos los elementos duplicados se encuentran necesariamente **agrupados en posiciones contiguas**. Esto elimina la necesidad de estructuras de búsqueda como Hash Sets (que serían $O(N)$ en espacio) y habilita el uso de la técnica de **Dos Punteros (Two Pointers)** con roles diferenciados:

1. **Puntero Lento (`k`)** — Actúa como el puntero de **escritura**. Marca la última posición del subarreglo de elementos únicos ya consolidados. Toda posición $\leq k$ contiene un valor único confirmado.
2. **Puntero Rápido (`i`)** — Actúa como el puntero de **lectura**. Recorre linealmente el arreglo explorando cada elemento.
3. En cada iteración, se compara el elemento en la posición de lectura (`nums[i]`) con el último elemento único registrado (`nums[k]`). Si son diferentes, se ha descubierto un nuevo valor único: se avanza `k` y se copia el valor de `i` a la nueva posición `k`.
4. Si son iguales, el puntero `i` simplemente avanza, omitiendo el duplicado sin realizar ninguna escritura.

Al finalizar el recorrido, las primeras $k + 1$ posiciones del arreglo contienen todos los elementos únicos en su orden original, y se retorna $k + 1$ como la longitud efectiva del subarreglo resultante.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Sin Guarda Explícita para Arreglo Vacío**: La solución en JS omite una verificación inicial para arreglos vacíos (`nums.length === 0`). Esto es correcto porque el bucle `for (let i = 1; i < nums.length; ...)` no se ejecuta si `nums.length` es `0` o `1`, y el retorno `k + 1` (donde `k = 0`) produce `1` para un arreglo de un solo elemento, o se comporta de forma aceptable si la entrada tiene al menos un elemento según las restricciones de LeetCode.
  - **Comparación Estricta (`!==`)**: Se emplea el operador de desigualdad estricta `!==` para comparar `nums[i]` con `nums[k]`, evitando cualquier coerción implícita de tipos por parte del motor de JavaScript y garantizando comparaciones de identidad pura entre enteros.
  - **Mutación Nativa por Referencia**: Los arreglos en JavaScript se pasan por referencia inherente al ser objetos. La asignación `nums[k] = nums[i]` modifica directamente el arreglo en el ámbito del llamador sin necesidad de anotaciones especiales en la firma de la función.

- **PHP**:
  - **Paso por Referencia Explícito (`&$nums`)**: PHP emplea semántica de _copy-on-write_ para arreglos, lo que significa que sin el operador `&`, cualquier modificación dentro de la función operaría sobre una copia local. El uso explícito de `&$nums` en la firma es obligatorio para cumplir con la restricción _in-place_ de LeetCode, garantizando que las mutaciones se reflejen directamente en el arreglo original del llamador.
  - **Guarda Defensiva para Arreglo Vacío**: La solución en PHP incluye una verificación explícita al inicio: `if ($length === 0) return 0;`. Esto protege contra un caso límite donde el acceso a `$nums[0]` resultaría en un _undefined offset_ si el arreglo estuviera vacío, adoptando un enfoque más defensivo que la versión en JavaScript.
  - **Precálculo de Longitud**: Se almacena `count($nums)` en la variable `$length` antes del bucle. Aunque `count()` opera en $O(1)$ en PHP (ya que la longitud se almacena como metadato en la estructura interna `zend_array`), esta práctica idiomática evita la reevaluación de la expresión en cada iteración del bucle y comunica claramente la intención del desarrollador.

## Lecciones Clave

- **Two Pointers con Roles Asimétricos (Lectura/Escritura)**: Cuando un arreglo ordenado contiene duplicados contiguos y debemos compactarlo _in-place_, el patrón de dos punteros con roles diferenciados (uno lento de escritura y uno rápido de lectura) es la solución canónica. Este mismo patrón se extiende directamente a variantes como _Remove Element_, _Move Zeroes_, o _Remove Duplicates from Sorted Array II_ (permitir hasta 2 ocurrencias), y es una herramienta fundamental en la manipulación eficiente de arreglos ordenados.
- **Explotar la Precondición de Ordenamiento**: Este ejercicio refuerza la disciplina de analizar las precondiciones de la entrada antes de elegir un algoritmo. La propiedad de ordenamiento transforma un problema que aparentemente requiere un `Set` o `Map` ($O(N)$ de espacio) en uno resoluble con $O(1)$ de memoria adicional, simplemente porque los duplicados están garantizados como adyacentes. Antes de recurrir a estructuras de datos complejas, siempre debemos preguntarnos: _¿qué propiedades de la entrada puedo explotar para simplificar la solución?_
