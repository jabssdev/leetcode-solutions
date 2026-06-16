# Contains Duplicate II

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud del arreglo `nums` -> Ambas soluciones realizan una única pasada lineal sobre el arreglo. Cada operación sobre la ventana deslizante (búsqueda, inserción y eliminación) es $O(1)$ amortizado — tanto `Set.has/add/delete` en JS como `isset/unset` sobre arrays asociativos en PHP operan en tiempo constante promedio gracias a su implementación interna por tablas de hash.
- **Espacio**: $O(\min(N, k))$ -> La ventana deslizante nunca contiene más de `k + 1` elementos simultáneamente. En el caso en que `k >= N`, la ventana puede crecer hasta el tamaño completo del arreglo; en caso contrario, se mantiene acotada por `k`. El espacio adicional es proporcional al tamaño de la ventana activa, no al tamaño total del input.

## Intuición y Enfoque

El problema exige determinar si existe algún par de índices `(i, j)` tales que `nums[i] == nums[j]` y `|i - j| <= k`. La restricción sobre la distancia índice transforma este problema en un escenario ideal para la técnica de **Ventana Deslizante con Conjunto (Sliding Window + Set)**.

La intuición central es mantener un conjunto que actúe como una **ventana de tamaño máximo `k`** que se desplaza a lo largo del arreglo. Al procesar el elemento `nums[i]`, si ya existe en la ventana, significa que hay un duplicado a una distancia de como máximo `k` índices (ya que la ventana solo retiene los últimos `k` elementos). Si la ventana supera el tamaño `k`, el elemento más antiguo (`nums[i - k]`) se expulsa antes de avanzar.

Este enfoque es ampliamente superior a la fuerza bruta naive $O(N \cdot k)$, que compararía cada elemento contra los `k` elementos precedentes en un doble bucle anidado. También es preferible a un enfoque de **Hash Map con índices** (donde se almacena `{valor: último_índice}`), ya que el conjunto de ventana deslizante garantiza que la condición `|i - j| <= k` se satisfaga **implícitamente** por la presencia del elemento en la ventana, sin necesidad de aritmética de índices adicional.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**: Utiliza la estructura nativa `Set` (ES6) como contenedor de la ventana. Los métodos `.has()`, `.add()` y `.delete()` son $O(1)$ promedio y proporcionan una API semántica y expressive para representar un conjunto de valores únicos. La propiedad `.size` es un getter nativo que devuelve el número de elementos actuales sin recorrer la estructura, a diferencia de un array convencional.

- **PHP**: Emula el comportamiento de un `Set` usando un **array asociativo** (`$window = []`) donde las claves son los valores del arreglo y los valores del mapa son simplemente `true`. Esto permite aprovechar `isset($window[$num])` para la búsqueda en $O(1)$ y `unset($window[$nums[$i - $k]])` para la eliminación en $O(1)$. Esta técnica es un patrón idiomático de PHP: dado que los arrays nativos en PHP son mapas ordenados, usar el valor como clave es la forma canónica de implementar un conjunto. Una diferencia crítica es que los valores del arreglo `$nums` deben ser compatibles como claves de array (enteros e strings funcionan directamente en este contexto).

## Lecciones Clave

- **Ventana Deslizante como Proxy de Restricción de Distancia**: Siempre que un problema imponga una restricción del tipo "dentro de los últimos `k` pasos", la ventana deslizante con un conjunto elimina la necesidad de rastrear índices explícitamente. La mera presencia de un elemento en la ventana certifica que fue visto dentro del rango requerido. Este patrón reaparece en problemas como *Sliding Window Maximum*, *Longest Substring Without Repeating Characters*, y *Minimum Size Subarray Sum*.

- **Set sobre HashMap cuando el índice no es necesario**: Si la condición de distancia puede delegarse a la estructura de la ventana (y no requiere calcular `j - i` con valores almacenados), un `Set` es semánticamente más limpio y eficiente en memoria que un `HashMap<valor, índice>`. Reserva el HashMap para variantes donde necesites verificar la distancia exacta en tiempo de ejecución, como en *Contains Duplicate III* con restricciones sobre la diferencia de valores.
