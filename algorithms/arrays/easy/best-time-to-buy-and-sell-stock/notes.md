# Best Time to Buy and Sell Stock

## Análisis de Complejidad

- **Tiempo**: $O(N)$ -> Tanto la solución en JavaScript como la de PHP realizan un único recorrido lineal (`for...of` y `foreach` respectivamente) sobre el arreglo de precios de longitud $N$. Dentro de cada iteración, todas las operaciones de comparación y asignación se ejecutan en tiempo constante $O(1)$, lo que resulta en una complejidad temporal directamente proporcional al tamaño de la entrada.
- **Espacio**: $O(1)$ -> El algoritmo es extremadamente eficiente en memoria ya que solo requiere dos variables primitivas auxiliares para realizar el seguimiento del precio mínimo (`minPrice`) y del beneficio máximo (`maxProfit`). No se utilizan estructuras de datos adicionales (como arreglos, mapas o conjuntos) ni llamadas recursivas que puedan incrementar el Call Stack.

## Intuición y Enfoque

El problema se resuelve de manera óptima utilizando un **enfoque codicioso (Greedy)** y una sola pasada (*Single Pass*). La intuición fundamental radica en que, para maximizar la ganancia, debemos comprar al precio más bajo posible y vender en un día posterior que maximice la diferencia. 

En lugar de evaluar todas las combinaciones posibles de días de compra y venta (que tomaría un tiempo ineficiente de $O(N^2)$), iteramos a través de los precios manteniendo dos estados dinámicos:
1. El precio mínimo visto hasta el día actual (`minPrice`).
2. El beneficio máximo obtenido hasta el momento (`maxProfit`).

Para cada precio en el arreglo, asumimos que es el día de venta. Evaluamos si el beneficio de vender hoy (`price - minPrice`) supera nuestro beneficio máximo registrado. Al mismo tiempo, actualizamos el precio mínimo si encontramos un valor menor para futuras transacciones. Este enfoque garantiza una solución en tiempo lineal y memoria constante.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Inicialización con `Infinity`**: Se utiliza el valor global `Infinity` para inicializar `minPrice`, asegurando que el primer precio evaluado siempre sea menor y actualice la variable correctamente sin necesidad de condicionales adicionales de inicialización.
  - **Iteración Declarativa**: Se emplea un bucle moderno `for (const price of prices)` para recorrer los valores del arreglo de forma limpia y directa.
  - **Métodos Nativos**: Utiliza `Math.min()` y `Math.max()` para encapsular la lógica de comparación de manera de forma declarativa, priorizando la legibilidad y expresividad del código.

- **PHP**:
  - **Límites de Tipos**: Al no poseer un equivalente directo e idéntico a `Infinity` en los tipos numéricos primitivos tradicionales de la misma manera que JS, se utiliza la constante predefinida del sistema `PHP_INT_MAX` para inicializar el precio de compra mínimo.
  - **Bucle `foreach`**: Se hace uso de `foreach ($prices as $price)` para iterar eficientemente sobre el arreglo indexado.
  - **Optimización por Bifurcación**: A diferencia de la versión de JavaScript que ejecuta ambas evaluaciones en cada iteración (`Math.min` y `Math.max`), la solución en PHP implementa una estructura `if ... elseif`. Si el precio actual es menor que el precio mínimo (`$price < $minPrice`), se actualiza el mínimo y se omite la evaluación de ganancia para esa iteración, puesto que vender el mismo día de compra resultaría en un beneficio de $0$, el cual nunca superará una ganancia acumulada positiva. Esta microoptimización reduce las operaciones aritméticas y comparaciones redundantes en tiempo de ejecución.

## Lecciones Clave

- **Optimización de Ventanas e Intervalos en una Pasada (Single Pass / Greedy)**: Cuando nos enfrentemos a problemas secuenciales que requieran encontrar diferencias extremas o emparejamientos óptimos (como compra/venta, mínimos/máximos locales, o subarrays de suma máxima), debemos buscar patrones donde podamos mantener un estado del "mínimo/máximo visto hasta ahora" para transformar algoritmos ineficientes de $O(N^2)$ en soluciones lineales $O(N)$.
- **Mutual Exclusividad y Microoptimización**: En algoritmos de alto rendimiento, identificar flujos lógicos mutuamente excluyentes (por ejemplo, si un precio es un nuevo mínimo histórico, es matemáticamente imposible que ese mismo día represente un punto de venta con ganancia máxima respecto al pasado) nos permite estructurar condicionales (`if / elseif`) que ahorran ciclos de CPU al evitar cálculos redundantes.
