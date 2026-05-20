# Intersection of Two Linked Lists

## Análisis de Complejidad

- **Tiempo**: $O(m + n)$ donde $m$ y $n$ son las longitudes de las listas `headA` y `headB` respectivamente -> Cada puntero recorre como máximo ambas listas completas antes de converger. El puntero `a` recorre los $m$ nodos de la lista A y luego hasta $n$ nodos de la lista B (o se encuentra con `b` antes). Simétricamente, `b` recorre $n + m$ nodos en el peor caso. Cada iteración del bucle `while` avanza al menos un puntero en tiempo constante $O(1)$, y el bucle termina en como máximo $m + n + 1$ iteraciones (incluyendo los dos saltos de redireccionamiento a la cabeza opuesta).
- **Espacio**: $O(1)$ -> El algoritmo utiliza exclusivamente dos variables de puntero (`a`/`pointerA` y `b`/`pointerB`). No se almacenan nodos visitados en conjuntos, mapas o arreglos auxiliares. No hay recursión ni crecimiento del Call Stack. El consumo de memoria es constante independientemente de la longitud de las listas.

## Intuición y Enfoque

El problema solicita encontrar el nodo donde dos listas enlazadas simples se intersectan (comparten el mismo nodo físico en memoria, no simplemente el mismo valor), o retornar `null` si no existe intersección.

Un enfoque con Hash Set almacenaría todos los nodos de una lista y luego verificaría la membresía de cada nodo de la otra lista, logrando $O(m + n)$ de tiempo pero con $O(m)$ o $O(n)$ de espacio. Otro enfoque calcularía las longitudes de ambas listas, alinearía los punteros avanzando el de la lista más larga, y luego recorrería ambas en paralelo, pero requiere dos pasadas previas de medición.

La solución implementa una técnica elegante de **Dos Punteros con Redireccionamiento Cruzado (Two Pointers with Cross-Redirect)**, basada en una propiedad aritmética brillante:

Si la lista A tiene una longitud de $a$ nodos antes de la intersección y la lista B tiene $b$ nodos antes de la intersección, con una sección compartida de longitud $c$:

- El puntero `a` recorre: $a + c + b$ nodos (lista A completa, luego lista B hasta la intersección).
- El puntero `b` recorre: $b + c + a$ nodos (lista B completa, luego lista A hasta la intersección).

Dado que $a + c + b = b + c + a$, **ambos punteros habrán recorrido exactamente la misma distancia total cuando se encuentren en el nodo de intersección**. La diferencia de longitudes entre las listas se neutraliza automáticamente al redirigir cada puntero a la cabeza de la lista opuesta cuando llega al final de la propia.

El flujo del algoritmo es:

1. Se inicializan dos punteros, uno en cada cabeza.
2. En cada iteración, cada puntero avanza al siguiente nodo. Si un puntero llega al final de su lista (`null`), se redirige a la cabeza de la lista **opuesta**.
3. Si las listas se intersectan, los punteros convergen en el nodo de intersección tras recorrer $a + c + b$ pasos cada uno.
4. Si las listas **no** se intersectan, ambos punteros llegan a `null` simultáneamente tras recorrer $m + n$ pasos (ya que $a + 0 + b = b + 0 + a$, donde $c = 0$), y el bucle termina retornando `null`.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Guarda con Evaluación Truthy/Falsy**: La validación inicial `if (!headA || !headB) return null;` aprovecha la coerción falsy de JavaScript, donde `null` se evalúa como `false`. Esto permite una sintaxis compacta para verificar si alguna de las cabezas es nula.
  - **Ternario con Evaluación Truthy del Nodo**: Dentro del bucle, `a = a ? a.next : headB` explota el hecho de que un objeto `ListNode` siempre es truthy en JavaScript, mientras que `null` es falsy. Cuando `a` es un nodo válido, avanza a `a.next`; cuando `a` es `null` (fin de lista), se redirige a `headB`. Esta sintaxis es más compacta que una comparación explícita contra `null`.
  - **Comparación de Identidad por Referencia (`!==`)**: La condición del bucle `a !== b` compara referencias de objetos, no valores. Dos nodos son iguales solo si apuntan a la **misma instancia en memoria**, que es exactamente la definición de intersección en este problema.

- **PHP**:
  - **Comparación Explícita contra `null`**: La solución en PHP utiliza comparaciones explícitas `$headA === null`, `$pointerA === null` en lugar de evaluaciones truthy/falsy. Esto es una práctica defensiva crucial en PHP ya que un objeto con propiedades que evalúen a `0`, `""` o `false` podría generar falsos negativos con el operador no estricto. El uso de `===` garantiza que solo `null` dispare el redireccionamiento.
  - **Nomenclatura Descriptiva de Punteros**: Mientras JavaScript utiliza variables cortas `a` y `b`, PHP adopta nombres más descriptivos `$pointerA` y `$pointerB`, mejorando la legibilidad y autodocumentación del código en un contexto donde PHP carece de las anotaciones de tipo JSDoc presentes en la versión de JavaScript.
  - **Orden de Evaluación en el Ternario**: La solución PHP evalúa primero si el puntero es `null` (`$pointerA === null ? $headB : $pointerA->next`), priorizando el caso de redireccionamiento. La versión en JS evalúa primero si el puntero es truthy (`a ? a.next : headB`), priorizando el caso de avance normal. Ambos son lógicamente equivalentes pero reflejan las convenciones idiomáticas de cada lenguaje.
  - **Acceso a Propiedades con `->` vs `.`**: PHP utiliza el operador de acceso a miembros de objeto `->` (`$pointerA->next`), mientras que JavaScript utiliza el operador punto (`.`). Esta diferencia sintáctica es puramente superficial y no afecta la semántica del algoritmo.

## Lecciones Clave

- **Neutralización de Asimetrías mediante Recorridos Cruzados**: Este ejercicio enseña una técnica profundamente elegante: cuando dos estructuras de datos tienen longitudes desiguales y necesitamos sincronizar la exploración, podemos neutralizar la diferencia haciendo que cada explorador recorra **ambas** estructuras. La suma total de pasos se iguala algebraicamente sin necesidad de medir las longitudes previamente. Este principio se aplica en sincronización de flujos de datos de diferentes tamaños, alineación de secuencias, y cualquier problema donde la asimetría de tamaño sea un obstáculo.
- **Convergencia Garantizada como Invariante de Diseño**: El algoritmo tiene una propiedad de terminación formal elegante: en un máximo de $m + n + 1$ iteraciones, los punteros convergen ya sea en el nodo de intersección o en `null`. No existe un caso de bucle infinito. Diseñar algoritmos con garantías formales de convergencia (en lugar de depender de heurísticas o condiciones de salida ad-hoc) es un estándar de calidad esencial en ingeniería de software de misión crítica.
