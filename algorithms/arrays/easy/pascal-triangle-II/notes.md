# Pascal's Triangle II

## Análisis de Complejidad

- **Tiempo**: $O(K)$ donde $K$ es `rowIndex` -> La solución es sumamente elegante ya que evita generar las filas anteriores del triángulo. El bucle `for` se ejecuta exactamente $K$ veces. Dentro del bucle, todas las operaciones aritméticas (multiplicación, resta, división y redondeo) se ejecutan en tiempo constante $O(1)$, lo que resulta en una complejidad temporal lineal respecto al índice de la fila solicitada.
- **Espacio**: $O(K)$ -> La complejidad de espacio es lineal para almacenar la salida de la fila que se va a retornar, la cual contiene exactamente $K + 1$ elementos. Si se analiza estrictamente el **espacio auxiliar de ejecución** (excluyendo el arreglo resultante de retorno), la complejidad es $O(1)$ constante, ya que no se crean arreglos temporales ni se utiliza recursión que afecte al Call Stack.

## Intuición y Enfoque

El enfoque dinámico convencional para resolver este problema requiere generar todas las filas previas del triángulo o bien utilizar un arreglo unidimensional de tamaño $K$ y actualizarlo de derecha a izquierda en dos bucles anidados, lo que resulta en una complejidad temporal de $O(K^2)$ y de espacio $O(K)$.

Sin embargo, esta solución implementa una optimización matemática superior utilizando la **Fórmula de Recurrencia para Coeficientes Binomiales (Combinatoria)**:
Cualquier elemento en la posición $i$ de la fila $n$ del Triángulo de Pascal representa el coeficiente binomial $\binom{n}{i}$, definido como:
$$\binom{n}{i} = \frac{n!}{i!(n-i)!}$$

A partir de esta definición, podemos derivar la relación de recurrencia que nos permite calcular el término actual a partir del término inmediatamente anterior:
$$\binom{n}{i} = \binom{n}{i-1} \cdot \frac{n - i + 1}{i}$$

Esta deducción matemática es la clave del algoritmo:

1. Empezamos la fila con el caso base `row[0] = 1`.
2. Para cada posición $i$ subsecuente (desde $1$ hasta $K$), calculamos su valor en tiempo constante $O(1)$ multiplicando el valor anterior `row[i-1]` por la fracción del coeficiente $\frac{K - i + 1}{i}$.
3. Al hacer esto en una sola pasada, reducimos drásticamente la complejidad temporal de $O(K^2)$ a $O(K)$ utilizando únicamente la memoria estrictamente necesaria para la respuesta.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Aritmética de Punto Flotante (IEEE 754)**: En JavaScript, todos los números se representan internamente como números de punto flotante de doble precisión. Al realizar la operación de división `/ i`, el motor puede introducir imprecisiones decimales infinitesimales (por ejemplo, `5.999999999999999` en lugar de `6`). Para subsanar esto, se utiliza `Math.round()` para garantizar que el valor empujado al arreglo mediante `.push()` sea el entero exacto esperado.
  - **Inserción Dinámica**: Se utiliza el método nativo `.push()` para ir construyendo el arreglo secuencialmente de izquierda a derecha.

- **PHP**:
  - **Moldeo Explícito a Entero (`(int)`)**: En PHP, la función `round()` realiza el redondeo matemático correcto pero devuelve un valor de tipo `float`. Dado que la firma del método requiere retornar un arreglo de enteros (`Integer[]`), se realiza un _casting_ o moldeo explícito a entero utilizando `(int)`. Esto asegura la integridad del tipo del dato almacenado, evitando que variables flotantes se infiltren en un arreglo tipado.
  - **Sintaxis de Agregación Eficiente**: Se utiliza el operador de corchete vacío `$row[] = ...`, el cual es un constructor interno de PHP optimizado para apilar elementos al final de un arreglo de forma más rápida que con llamadas a funciones de librería.

## Lecciones Clave

- **Optimización Matemática frente a Programación Dinámica**: En problemas de secuencias de combinatoria o algebraicas, antes de aplicar técnicas comunes de memorización o tabulación (DP), es fundamental analizar si existe una relación de recurrencia directa de un solo paso. Explotar estas propiedades matemáticas puede colapsar algoritmos polinomiales $O(N^2)$ a complejidades lineales $O(N)$ o incluso constantes $O(1)$.
- **Programación Defensiva contra Pérdida de Precisión**: Cuando implementamos algoritmos matemáticos donde teóricamente el resultado final es un número entero pero el camino de cálculo requiere divisiones y números reales, es una regla de oro de la ingeniería de software aplicar redondeos y conversiones de tipo explícitas (`Math.round`, casts). Esto neutraliza los problemas inherentes a la especificación de punto flotante IEEE 754 presentes en los procesadores modernos.
