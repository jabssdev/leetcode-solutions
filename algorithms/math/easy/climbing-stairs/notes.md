# Climbing Stairs

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es el número de escalones -> El bucle `for` se ejecuta exactamente $N - 1$ veces (desde `i = 2` hasta `i = n`). En cada iteración, se realizan únicamente operaciones de suma y reasignación de variables escalares, todas en tiempo constante $O(1)$. No hay recursión ni llamadas adicionales, por lo que la complejidad temporal es estrictamente lineal.
- **Espacio**: $O(1)$ -> El algoritmo implementa la versión **optimizada en espacio** de la Programación Dinámica. En lugar de mantener una tabla completa de $N$ estados, solo se conservan en memoria los **dos últimos valores** (`prev` y `curr`), que son las únicas dependencias necesarias para calcular el siguiente estado. El consumo de memoria es constante independientemente del valor de `n`.

## Intuición y Enfoque

El problema solicita contar el número de formas distintas de subir `n` escalones tomando 1 o 2 escalones por vez. Un enfoque de fuerza bruta mediante recursión exploraría todas las combinaciones posibles, produciendo un árbol de llamadas con subestructura redundante masiva: `f(n)` calcula `f(n-1)` y `f(n-2)`, cada uno de los cuales recalcula `f(n-2)` y `f(n-3)` respectivamente, resultando en una complejidad exponencial $O(2^N)$.

La clave del problema es identificar la **relación de recurrencia subyacente**: el número de formas de llegar al escalón $n$ es exactamente igual a la suma de las formas de llegar al escalón $n-1$ (tomando 1 escalón adicional) más las formas de llegar al escalón $n-2$ (tomando 2 escalones adicionales):

$$f(n) = f(n-1) + f(n-2)$$

con casos base $f(1) = 1$ y $f(2) = 2$.

Esta recurrencia es idéntica a la de la **Secuencia de Fibonacci**, y la solución implementa **Programación Dinámica Optimizada en Espacio (Space-Optimized DP)** mediante tabulación de dos variables deslizantes (_rolling variables_):

1. Se inicializan `prev = 1` y `curr = 1`, representando $f(1) = 1$ y $f(2) = 1$ antes del primer escalón de cómputo.
2. En cada iteración del bucle (desde `i = 2` hasta `i = n`), el siguiente valor se calcula como `next = prev + curr`, luego `prev` toma el valor de `curr` y `curr` toma el valor de `next`.
3. Al finalizar, `curr` contiene $f(n)$.

Este enfoque elimina la redundancia computacional de la recursión ingenua, llevando la complejidad de $O(2^N)$ a $O(N)$ en tiempo y de $O(N)$ (tabla DP completa) a $O(1)$ en espacio.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Declaración `const` para `next`**: Dentro del bucle, `const next = prev + curr` declara la variable como constante de bloque. Esto comunica que el valor calculado no cambiará dentro de la iteración actual y limita su ámbito de vida a dicha iteración, reforzando la intención del código y permitiendo que el motor V8 optimice la asignación.
  - **Sin Guarda para `n <= 1`**: La solución en JavaScript omite una validación explícita para `n = 1`. Esto es correcto porque cuando `n = 1`, el bucle `for (let i = 2; i <= 1; ...)` no se ejecuta ninguna vez y la función retorna `curr = 1`, que es el valor correcto. Los casos base están implícitamente manejados por la inicialización y la condición del bucle.
  - **Estructura Compacta de Rotación**: La rotación de variables utiliza la variable temporal `next` para preservar el valor actual antes de la reasignación: `next = prev + curr`, `prev = curr`, `curr = next`. Esta es la forma idiomática en JavaScript que no requiere desestructuración.

- **PHP**:
  - **Guarda Defensiva Explícita `$n <= 1`**: La versión PHP incluye una verificación inicial `if ($n <= 1) return 1;`. Aunque el bucle también maneja correctamente `n = 1` sin esta guarda, la verificación explícita es una práctica defensiva que documenta el caso base como intención del diseño del algoritmo, separándolo visualmente de la lógica de iteración principal.
  - **Variable Temporal `$temp` en lugar de `const next`**: PHP no tiene `const` para variables locales de función. La rotación se implementa guardando el valor actual en `$temp = $curr` antes de actualizar `$curr = $prev + $curr` y luego asignando `$prev = $temp`. El orden de las operaciones difiere superficialmente de la versión JS (que calcula `next` primero), pero el resultado matemático es idéntico.
  - **Orden de Actualización Diferente**: JS calcula `next = prev + curr` y luego rota (`prev = curr`, `curr = next`). PHP actualiza directamente `$curr = $prev + $curr` y guarda el viejo `$curr` en `$temp` **antes** de la actualización para asignarlo a `$prev`. Ambas estrategias de rotación son equivalentes y correctas; la diferencia es únicamente de estilo idiomático.

## Lecciones Clave

- **Space-Optimized DP: De Tabla $O(N)$ a Variables Deslizantes $O(1)$**: Este ejercicio ilustra la optimización de espacio más importante en Programación Dinámica por tabulación: cuando la recurrencia de un problema solo depende de un número **fijo y pequeño de estados anteriores** (en este caso, los dos últimos), no es necesario mantener una tabla completa de $N$ entradas. Dos variables deslizantes (_rolling variables_) son suficientes. Este principio se aplica directamente en _House Robber_, _Min Cost Climbing Stairs_, _Tribonacci Number_, y cualquier DP lineal con ventana de dependencia constante.
- **Reconocimiento de la Fibonacci Oculta como Señal de DP Lineal**: La secuencia de Fibonacci no es un problema aislado; es una **firma recurrente** que aparece en problemas de conteo de caminos, decisiones binarias secuenciales, y particiones con restricciones simples. Cuando la relación de recurrencia de un problema adopta la forma $f(n) = f(n-1) + f(n-2)$ (o variantes con más términos), la solución óptima es siempre una DP lineal con variables deslizantes. Internalizar este reconocimiento de patrón permite resolver familias enteras de problemas sin derivar la solución desde cero.
