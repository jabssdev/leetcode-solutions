# Merge Sorted Array

## Análisis de Complejidad

- **Tiempo**: $O(m + n)$ -> El algoritmo procesa los elementos en un solo recorrido de derecha a izquierda. En el peor de los casos, realizaremos exactamente $m + n$ iteraciones del bucle `while` para colocar todos los elementos en sus posiciones finales. En cada iteración, realizamos únicamente comparaciones aritméticas básicas e intercambios que se ejecutan en tiempo constante $O(1)$, lo que resulta en una complejidad de tiempo lineal respecto al tamaño combinado de ambos arreglos.
- **Espacio**: $O(1)$ -> La fusión se realiza estrictamente **in-place** (dentro del espacio previamente asignado al final del arreglo `nums1`). Solo se definen tres variables de puntero numérico (`p1`, `p2`, `p`) en la pila de ejecución. No se crean arreglos auxiliares, mapas o listas adicionales, lo que asegura que el consumo de memoria permanezca constante.

## Intuición y Enfoque

La fusión estándar de dos arreglos ordenados utilizando la técnica clásica de **Dos Punteros (Two Pointers)** normalmente se realiza de izquierda a derecha. Sin embargo, aplicar este enfoque tradicional *in-place* en `nums1` requeriría desplazar continuamente los elementos hacia la derecha para evitar sobrescribirlos, lo que elevaría la complejidad temporal a un costoso $O(m \cdot n)$ debido a los costes de desplazamiento de memoria. Para evitar esto con espacio $O(1)$, se requeriría espacio temporal adicional de $O(m)$ para guardar una copia de `nums1`.

El enfoque óptimo rompe esta limitación mediante el uso de **Tres Punteros desde el Extremo Posterior (Right-to-Left / Rear-to-Front Two Pointers)**:
1. Dado que `nums1` viene pre-dimensionado con un tamaño físico de $m + n$ (donde los últimos $n$ espacios están vacíos o rellenos con ceros), el final de `nums1` es una "zona segura" libre de colisiones.
2. Posicionamos los punteros de lectura al final de las secciones útiles de cada arreglo: `p1` en la posición $m - 1$ de `nums1` y `p2` en $n - 1$ de `nums2`. El puntero de escritura `p` se sitúa en el extremo final de `nums1` ($m + n - 1$).
3. Comparamos los elementos más grandes en los extremos de lectura. El valor mayor se copia en la posición `p` y se decrementa el puntero correspondiente y el de escritura `p`.
4. **Optimización Clave**: El bucle se controla únicamente bajo la condición `p2 >= 0`. Si `p2` (el puntero de `nums2`) se agota antes, los elementos restantes de `nums1` ya se encuentran ordenados en sus posiciones correctas, por lo que no es necesario realizar ninguna operación de copia adicional.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Optimización de Salida Temprana**: Se incluye un control preventivo al inicio: `if (n === 0) return;`. Si `nums2` está vacío, la fusión es nula y el algoritmo termina de forma inmediata sin inicializar punteros ni evaluar condicionales.
  - **Mutabilidad por Referencia Nativa**: En JavaScript, los arreglos se pasan por referencia. Cualquier asignación a `nums1[p]` modifica el arreglo original directamente en el ámbito global del llamador.

- **PHP**:
  - **Paso por Referencia Explícito (`&`)**: PHP maneja los arreglos por valor (bajo el mecanismo interno *copy-on-write*) a menos que se indique lo contrario. Para cumplir con la restricción *in-place* de LeetCode, la firma de la función requiere el operador de referencia explícito: `&$nums1`. Sin el `&`, las modificaciones solo afectarían a una copia local de la función y no al arreglo original.
  - **Elegancia de Control**: A diferencia de la versión en JS, la solución en PHP no incluye una salida temprana explícita para `$n === 0`. No obstante, el diseño del bucle es inherentemente elegante: si `$n === 0`, el puntero `$p2` se inicializa como `-1`, haciendo que la condición del `while ($p2 >= 0)` falle de inmediato y termine la ejecución de manera limpia sin alterar el arreglo, logrando el mismo efecto de forma implícita.

## Lecciones Clave

- **Punteros Inversos en Operaciones In-Place**: Siempre que se deba modificar una estructura de datos *in-place* sin sobreescribir información útil ni pagar costes por desplazamientos ($O(N)$ por inserción), debemos explorar la posibilidad de procesar la entrada de atrás hacia adelante (de derecha a izquierda). Este patrón es sumamente útil en manipulación de arreglos y cadenas.
- **Optimización por Condición de Parada Asimétrica**: No todas las iteraciones que involucran múltiples punteros deben durar hasta que todos los punteros se agoten. En este ejercicio, identificar que el arreglo destino es el propio `nums1` nos permite detener el proceso tan pronto como `nums2` (`p2 < 0`) esté completamente vacío, ahorrando pasos innecesarios y demostrando una comprensión profunda de la estructura de los datos involucrados.
