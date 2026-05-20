# Pascal's Triangle

## Análisis de Complejidad

- **Tiempo**: $O(N^2)$ donde $N$ es `numRows` -> La estructura del Triángulo de Pascal genera una progresión aritmética de elementos. La primera fila tiene $1$ elemento, la segunda $2$, la tercera $3$, y así sucesivamente hasta la fila $N$. El número total de iteraciones internas (y por ende de sumas y escrituras) viene dado por la suma de la serie aritmética: $\sum_{i=1}^{N} i = \frac{N(N+1)}{2}$. Como cada operación de suma y asignación toma tiempo constante $O(1)$, la complejidad temporal global es cuadrática respecto al número de filas solicitado.
- **Espacio**: $O(N^2)$ (incluyendo la estructura de salida) -> El algoritmo almacena todos los valores calculados en una matriz bidimensional dinámicamente asignada. Al final del proceso, la estructura contiene exactamente $\frac{N(N+1)}{2}$ enteros. Si se analiza estrictamente el **espacio auxiliar de ejecución** (excluyendo el contenedor del resultado de retorno que exige el problema), la complejidad de espacio es $O(N)$ puesto que solo requerimos la referencia temporal de la fila anterior (`prevRow`) para construir la actual.

## Intuición y Enfoque

El problema consiste en generar el Triángulo de Pascal hasta un número determinado de filas. La regla de construcción establece que cada número en el triángulo es igual a la suma de los dos números situados inmediatamente encima de él.

Este ejercicio representa un ejemplo clásico y perfecto de **Programación Dinámica (Dynamic Programming)** bajo el enfoque de **Tabulación (Bottom-Up)**:

1. Definimos una base de caso sumamente simple: la primera fila es siempre `[1]`.
2. Para construir cualquier celda subsecuente `newRow[j]` en una fila `i`, no realizamos cálculos recursivos costosos (lo cual llevaría a un árbol de llamadas exponencial y redundante). En su lugar, reutilizamos los subproblemas ya resueltos y almacenados en la fila anterior: `prevRow[j - 1] + prevRow[j]`.
3. Reconocemos que las fronteras de cada fila (el primer y el último elemento) no tienen dos elementos superiores para sumar; matemáticamente siempre valen `1`. Por lo tanto, inicializamos cada fila con `[1]`, calculamos los elementos intermedios mediante la relación de recurrencia DP en un bucle acotado, y cerramos la fila añadiendo un `1` al final.

Este enfoque asegura que cada número del triángulo se calcule exactamente una única vez, logrando la máxima eficiencia posible para este problema.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Arreglos Multidimensionales Flexibles**: JavaScript implementa matrices bidimensionales como arreglos de arreglos (`Array of Arrays`). Al ser colecciones dinámicas no estructuradas de forma rígida, permiten la mutación directa y el crecimiento elástico de sus dimensiones en tiempo de ejecución.
  - **Método `.push()`**: Se utiliza `Array.prototype.push()` para agregar elementos a la fila (`newRow.push(...)`) y para anexar la fila completada al triángulo principal (`triangle.push(...)`). Esta operación es altamente optimizada en motores como V8 para escrituras secuenciales en arreglos continuos en memoria.

- **PHP**:
  - **Operador de Asignación Corta (`[]`)**: PHP ofrece la sintaxis compacta `$newRow[] = ...` y `$triangle[] = ...` para apilar elementos al final de un arreglo. En términos de rendimiento del motor Zend, esta sintaxis es un constructor interno del lenguaje que supera en velocidad a la llamada de función `array_push()`, al evitar la sobrecarga del stack de llamadas de funciones de PHP.
  - **Estructura Interna de Arreglos**: En PHP, los arreglos son en realidad tablas hash ordenadas (mapas asociativos). Cuando se utilizan sin claves explícitas, el motor gestiona de manera interna punteros e índices enteros correlativos, simulando listas dinámicas con un comportamiento idéntico y natural al de los arreglos en JavaScript.

## Lecciones Clave

- **El Concepto de Tabulación en Programación Dinámica (Bottom-Up DP)**: Este ejercicio refuerza el hábito de buscar relaciones de recurrencia en problemas secuenciales. Si el cálculo del estado actual depende directamente de combinaciones de estados previos inmediatos, la tabulación nos permite construir soluciones de manera progresiva y eficiente, eliminando por completo la redundancia computacional mediante el almacenamiento estructurado.
- **Manejo de Fronteras o Casos Límite en Generación de Estructuras**: Al construir estructuras complejas paso a paso, separar los elementos de frontera (los extremos `1` del triángulo que requieren reglas condicionales o que carecen de vecinos) de los elementos internos (que siguen la regla matemática general) simplifica enormemente la lógica de control. Inicializar con el límite izquierdo y cerrar con el derecho evita condicionales `if` costosos dentro del bucle interno.
