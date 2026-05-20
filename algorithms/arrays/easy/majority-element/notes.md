# Majority Element

## Análisis de Complejidad

- **Tiempo**: $O(N)$ -> Ambas soluciones (JavaScript y PHP) realizan una sola pasada lineal sobre el arreglo de precios/números (`for...of` y `foreach` respectivamente) de tamaño $N$. Cada iteración consta únicamente de operaciones aritméticas simples, condicionales lógicos y asignaciones directas, las cuales se ejecutan en tiempo constante $O(1)$. Por tanto, la complejidad de tiempo es lineal respecto al número de elementos.
- **Espacio**: $O(1)$ -> El algoritmo implementado es el **Algoritmo de Votación de Boyer-Moore**, el cual destaca por su consumo de memoria ultra eficiente. Solo se emplean dos variables escalares en cada lenguaje para llevar el registro del candidato actual (`candidate`) y el contador de votos (`count`). Al no utilizar arrays auxiliares, tablas hash o recursión, no hay consumo adicional en el montón (Heap) ni en la pila de llamadas (Call Stack), garantizando un consumo de memoria constante.

## Intuición y Enfoque

El problema nos pide encontrar el elemento mayoritario que aparece estrictamente más de $\lfloor N/2 \rfloor$ veces en un arreglo. Una aproximación intuitiva inicial podría ser el uso de una tabla de frecuencia (Hash Map) para contar las ocurrencias de cada elemento, lo cual tomaría $O(N)$ de tiempo pero requeriría $O(N)$ de espacio auxiliar. Otra opción sería ordenar el arreglo y devolver el elemento central en $nums[\lfloor N/2 \rfloor]$, tomando $O(N \log N)$ de tiempo.

Para alcanzar el óptimo absoluto de $O(N)$ en tiempo y $O(1)$ en espacio, se utiliza el **Algoritmo de Votación de Boyer-Moore**.

La intuición fundamental de este algoritmo es una **guerra de desgaste o cancelación de votos**:

1. Visualizamos el arreglo como una votación donde el elemento mayoritario y los demás elementos compiten. Dado que el elemento mayoritario aparece más de la mitad de las veces, incluso si todos los demás elementos se aliaran para votar en su contra (cancelar sus votos), el mayoritario siempre tendrá al menos un voto a su favor restante al final.
2. Mantenemos un `candidate` y un contador `count`.
3. Si el `count` llega a 0, significa que los votos a favor de nuestro candidato han sido completamente anulados por elementos distintos; por tanto, establecemos el elemento actual como el nuevo `candidate`.
4. Si el elemento actual es igual al `candidate`, incrementamos el `count` en $1$ (voto de apoyo); si es diferente, lo decrementamos en $1$ (voto de cancelación).

Al final de la iteración única, el `candidate` retornado es garantizadamente el elemento mayoritario (bajo la precondición de que este siempre existe en la entrada).

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Inicialización Directa**: Se inicializa `candidate` directamente con el primer elemento (`nums[0]`), optimizando la lógica del bucle desde la primera lectura ya que se tiene un candidato válido de inmediato.
  - **Comparación Estricta**: Se utiliza el operador de igualdad estricta `===` (`num === candidate`) en la expresión ternaria para evitar cualquier coerción implícita de tipos no deseada por parte del motor de JS, garantizando una comparación limpia y directa de enteros.
  - **Bucle Moderno**: El uso de `for (const num of nums)` permite un recorrido limpio sin necesidad de manejar índices numéricos manuales ni realizar accesos por índice dentro del cuerpo del ciclo.

- **PHP**:
  - **Inicialización Neutra**: Se inicializa `$candidate = null` y `$count = 0`. Esto significa que la primera iteración evalúa obligatoriamente `$count === 0` como verdadero, asignando el primer elemento en tiempo de ejecución. Ambas estrategias de inicialización (JS y PHP) son válidas y conducen al mismo resultado final.
  - **Comparación Estricta en PHP**: Al igual que en JS, se emplea el operador de identidad `===` (`$num === $candidate`) dentro del operador ternario. Esto es especialmente crucial en PHP para mitigar su comportamiento histórico de coerción débil de tipos (evitando que valores numéricos como `0` colisionen incorrectamente con estados iniciales de tipo `null` o falsos positivos con otros tipos escalares).
  - **Bucle `foreach`**: Se utiliza `foreach ($nums as $num)` para realizar la iteración sobre el arreglo de enteros de manera idiomática y de alto rendimiento.

## Lecciones Clave

- **El Algoritmo de Votación de Boyer-Moore**: Es el patrón definitivo de diseño de algoritmos para resolver problemas de detección de mayorías y frecuencias dominantes en flujo de datos (Stream) o colecciones estáticas. Debe ser tu primera opción cuando te soliciten identificar un elemento mayoritario que represente más del 50% de la muestra sin incurrir en costes de memoria lineal.
- **La Fuerza de la Cancelación en Reducción de Espacio**: Este ejercicio nos enseña que ciertos problemas que parecen requerir almacenamiento global de frecuencias (como tablas hash) pueden optimizarse drásticamente si somos capaces de explotar propiedades matemáticas específicas de la entrada (en este caso, la dominancia matemática de $> 50\%$). Diseñar algoritmos basados en leyes de cancelación es una herramienta avanzada para ingenieros que manejan grandes volúmenes de datos.
