# Search Insert Position

## Análisis de Complejidad

- **Tiempo**: $O(\log N)$ donde $N$ es la longitud del arreglo `nums` -> El algoritmo implementa una **Búsqueda Binaria** clásica. En cada iteración del bucle `while`, el espacio de búsqueda se reduce exactamente a la mitad al mover el puntero `left` o `right` hacia el punto medio `mid`. Esto resulta en un máximo de $\lceil \log_2 N \rceil$ iteraciones. Dentro de cada iteración, las operaciones de cálculo del punto medio, comparación y reasignación se ejecutan en tiempo constante $O(1)$, garantizando una complejidad temporal logarítmica.
- **Espacio**: $O(1)$ -> El algoritmo opera exclusivamente con tres variables escalares de índice: `left`, `right` y `mid`. No se crean arreglos auxiliares, estructuras de datos adicionales ni se utiliza recursión que incremente el Call Stack. El consumo de memoria es constante independientemente del tamaño de la entrada.

## Intuición y Enfoque

El problema solicita encontrar el índice de un valor objetivo (`target`) en un arreglo ordenado de enteros sin duplicados, o bien determinar la posición donde debería insertarse para mantener el orden. LeetCode requiere explícitamente un algoritmo con complejidad $O(\log N)$, descartando de inmediato un recorrido lineal $O(N)$.

La solución implementa el algoritmo de **Búsqueda Binaria (Binary Search)** con la variante de **Lower Bound (Límite Inferior)**:

1. Se inicializan dos punteros que delimitan el espacio de búsqueda: `left = 0` y `right = N - 1`.
2. Mientras el espacio de búsqueda sea válido (`left <= right`), se calcula el punto medio `mid`.
3. Se evalúan tres escenarios:
   - Si `nums[mid] === target`, el valor fue encontrado y se retorna `mid` inmediatamente.
   - Si `nums[mid] < target`, el objetivo se encuentra en la mitad derecha, por lo que se descarta la mitad izquierda moviendo `left = mid + 1`.
   - Si `nums[mid] > target`, el objetivo se encuentra en la mitad izquierda, por lo que se descarta la mitad derecha moviendo `right = mid - 1`.
4. **La clave del problema**: Si el bucle termina sin encontrar el `target` (es decir, `left > right`), el puntero `left` converge naturalmente a la **posición exacta de inserción**. Esto ocurre porque `left` siempre apunta al primer elemento que es mayor o igual al `target` al finalizar, lo cual es precisamente la definición de *lower bound* en búsqueda binaria.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Cálculo Seguro del Punto Medio con Operador Bitwise**: Se utiliza la expresión `left + ((right - left) >> 1)` para calcular `mid`. La operación `>> 1` (desplazamiento de bits a la derecha) es funcionalmente equivalente a dividir por 2 y truncar, pero opera a nivel de registros del procesador con un rendimiento superior a la división aritmética. Además, la fórmula `left + ((right - left) >> 1)` previene el desbordamiento de enteros (*integer overflow*) que ocurriría con la expresión ingenua `(left + right) / 2` cuando `left + right` excede `Number.MAX_SAFE_INTEGER` ($2^{53} - 1$).
  - **Flujo Condicional con `else if`**: La evaluación de los tres escenarios se encadena en una estructura `if ... else if ... else`, garantizando exclusión mutua y evaluación de cortocircuito entre las ramas sin comparaciones redundantes.

- **PHP**:
  - **División Entera con Cast Explícito**: PHP carece de un operador nativo de desplazamiento de bits que sea idiomático para este propósito en la comunidad PHP. En su lugar, se utiliza `(int)(($right - $left) / 2)`. El operador de división `/` en PHP retorna un `float` cuando el resultado no es un entero exacto, por lo que el cast `(int)` es obligatorio para truncar el valor decimal y obtener un índice entero válido. Sin este cast, acceder a `$nums[$mid]` con un índice flotante generaría un comportamiento inesperado.
  - **Estructura Condicional Separada**: A diferencia de la versión en JS que encadena `if ... else if ... else`, la solución en PHP separa la verificación de igualdad (`$nums[$mid] === $target`) en un bloque `if` independiente, seguido de un segundo bloque `if ... else` para las ramas de dirección. Ambos enfoques son funcionalmente equivalentes, pero la versión en PHP prioriza la claridad visual al aislar el caso de éxito (hallazgo del target) de la lógica de reducción del espacio de búsqueda.
  - **Comparación Estricta (`===`)**: Se emplea el operador de identidad estricta `===` para las comparaciones, evitando la coerción débil de tipos de PHP (por ejemplo, `0 == false` → `true`) y garantizando comparaciones seguras de valor y tipo.

## Lecciones Clave

- **Búsqueda Binaria como Paradigma Fundacional**: Este ejercicio refuerza la búsqueda binaria no solo como un algoritmo de búsqueda, sino como un **paradigma de reducción logarítmica del espacio de estados**. Cada vez que un problema presente un dominio ordenado y monotónico donde podemos descartar sistemáticamente la mitad del espacio en cada paso, la búsqueda binaria es la herramienta de primer recurso. Se aplica en búsqueda de valores, posiciones de inserción, raíces cuadradas, capacidades mínimas, y una amplia familia de problemas de optimización sobre funciones monótonas.
- **La Convergencia del Puntero `left` como Posición de Inserción**: Una lección sutil pero poderosa es comprender que, al finalizar una búsqueda binaria sin éxito, el estado de los punteros no es arbitrario: `left` converge al *lower bound* (primer índice donde `nums[i] >= target`). Internalizar esta propiedad evita la necesidad de lógica post-búsqueda adicional y permite resolver variantes como *Find First and Last Position*, *Search in Rotated Sorted Array*, o *Find Minimum in Rotated Sorted Array* reutilizando la misma estructura base.
