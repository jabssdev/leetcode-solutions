# Happy Number

## Análisis de Complejidad

- **Tiempo**: $O(\log N)$ -> La función `getNext(n)` extrae y eleva al cuadrado los dígitos de `n` en $O(\log_{10} N)$ iteraciones (proporcional al número de dígitos). La secuencia de sumas de cuadrados de dígitos para números no felices está matemáticamente demostrada que colapsa rápidamente a valores pequeños (por debajo de ~243 para cualquier entero de 3 dígitos) y luego entra en un ciclo cerrado conocido que incluye el número `4`. El algoritmo de Floyd detecta este ciclo en un número constante y pequeño de llamadas a `getNext`, resultando en una complejidad temporal global de $O(\log N)$ dominada por el procesamiento inicial de los dígitos del valor de entrada.
- **Espacio**: $O(1)$ -> La aplicación del algoritmo de Floyd (Tortuga y Liebre) al espacio de estados de la secuencia elimina la necesidad de un conjunto o mapa para rastrear los valores ya visitados. Solo se mantienen dos variables escalares de estado: `slow` y `fast`. La función `getNext` también opera en $O(1)$ de espacio auxiliar. No se crean estructuras de datos adicionales de tamaño variable.

## Intuición y Enfoque

Un número es "feliz" (_happy_) si la aplicación repetida de la función "suma de cuadrados de sus dígitos" converge eventualmente a `1`. Si el número no es feliz, la secuencia inevitablemente entra en un ciclo infinito que **nunca** alcanza `1`.

Un enfoque con Hash Set almacenaría cada número visitado y verificaría la repetición, pero consume $O(N)$ de espacio en el peor caso y requiere conocer cuándo el ciclo se completa.

La solución implementa una aplicación creativa del **Algoritmo de Detección de Ciclos de Floyd (Tortuga y Liebre)**, usualmente asociado a listas enlazadas, pero igualmente aplicable a cualquier **función iterada** (una función que se aplica repetidamente sobre su propio resultado). Aquí, la "lista enlazada" implícita es la secuencia de valores generados por `getNext`:

$$n \to \text{getNext}(n) \to \text{getNext}(\text{getNext}(n)) \to \ldots$$

1. Se inicializan `slow = n` y `fast = getNext(n)`.
2. En cada iteración, `slow` avanza **un** paso (`slow = getNext(slow)`) y `fast` avanza **dos** pasos (`fast = getNext(getNext(fast))`).
3. El bucle termina cuando ocurre una de dos condiciones:
   - `fast === 1`: la secuencia rápida alcanzó `1`, el número es feliz → `return true`.
   - `slow === fast`: ambos punteros convergieron en el mismo valor dentro del ciclo → el número no es feliz → `return false`.

**La elegancia de la condición de terminación dual**: no necesitamos saber en qué número del ciclo se encuentran los punteros; simplemente verificamos si el punto de convergencia es `1` o no. Si `fast` llega a `1`, sabemos que la secuencia es convergente. Si `slow` alcanza a `fast` en cualquier otro valor, sabemos que estamos atrapados en un ciclo que excluye al `1`.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Función Auxiliar a Nivel de Módulo**: `getNext` se declara como una función de nivel de módulo (hoisted), no como método de un objeto. Esto es posible porque JavaScript eleva las declaraciones `function` al tope de su ámbito, permitiendo que `getNext` sea llamada desde `isHappy` aunque esté definida después en el código. La función es efectivamente "estática" en el sentido de que no depende de ningún estado externo.
  - **Comparación Estricta (`!==` y `===`)**: La condición del bucle `fast !== 1 && slow !== fast` y el retorno `fast === 1` usan el operador de identidad estricta para comparar números primitivos, evitando cualquier coerción implícita de tipos por parte del motor JavaScript.
  - **`Math.floor(n / 10)`**: Se usa `Math.floor()` para la división entera al extraer dígitos dentro de `getNext`. Dado que JavaScript representa todos los números como `float64`, la división `/` produce un número de punto flotante, y `Math.floor()` lo trunca al entero correcto.

- **PHP**:
  - **Método de Instancia con `$this->`**: En PHP, `getNext` se declara como un método de la clase `Solution`. Las llamadas dentro de `isHappy` requieren el prefijo `$this->getNext(...)`, que es el mecanismo estándar de invocación de métodos de instancia en PHP. A diferencia de JavaScript donde la función es de alcance global, PHP encapsula la lógica auxiliar dentro de la clase.
  - **División Entera con Cast `(int)`**: PHP usa `(int)($n / 10)` para la división entera en `getNext`. El operador `/` de PHP retorna `float` cuando el resultado no es exacto (aunque en la práctica `intdiv()` sería más idiomático en PHP 7+). El cast `(int)` trunca el valor flotante al entero correcto. Ambas formas producen el mismo resultado para enteros positivos.
  - **Isomorfismo Algorítmico Total**: La lógica de `isHappy` y `getNext` es estructuralmente idéntica entre ambas implementaciones. La diferencia más sustantiva es el mecanismo de llamada a la función auxiliar (`getNext(x)` vs `$this->getNext($x)`), el mecanismo de división entera (`Math.floor` vs `(int)` cast), y las convenciones sintácticas habituales de cada lenguaje.

## Lecciones Clave

- **Floyd Aplicado a Funciones Iteradas, No Solo a Listas**: El Algoritmo de Floyd (Tortuga y Liebre) trasciende la detección de ciclos en listas enlazadas. Es aplicable a **cualquier secuencia generada por una función determinista iterada** $f: X \to X$ donde el dominio es finito y acotado. Siempre que tengamos una función que se aplica repetidamente a su propio resultado y necesitemos determinar si la secuencia converge a un punto fijo o entra en un ciclo, Floyd es la herramienta óptima con $O(1)$ de espacio adicional.
- **La Condición de Terminación Dual como Patrón de Clasificación**: La condición `while (fast !== 1 && slow !== fast)` encapsula dos clases de terminación mutuamente excluyentes: convergencia al objetivo (`fast === 1`) o detección de ciclo (`slow === fast`). Este patrón de "terminar por éxito o por bucle detectado" es aplicable en cualquier algoritmo de búsqueda o simulación sobre espacios de estados finitos donde solo existen dos resultados posibles: encontrar el objetivo o demostrar que es inalcanzable mediante la presencia de un ciclo.
