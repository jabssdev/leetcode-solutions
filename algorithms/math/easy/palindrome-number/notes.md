# Palindrome Number

## Análisis de Complejidad

- **Tiempo**: $O(\log_{10} N)$ donde $N$ es el valor absoluto de `x` -> El algoritmo solo procesa la **mitad** de los dígitos del número. El bucle `while (x > revertedNumber)` se ejecuta exactamente $\lfloor d/2 \rfloor$ veces, donde $d = \lfloor \log_{10} N \rfloor + 1$ es el número de dígitos de `x`. En cada iteración, se extraen y procesan dígitos en tiempo constante $O(1)$. La complejidad es por tanto la mitad del $O(\log N)$ esperado para procesar todos los dígitos, aunque asintóticamente se sigue expresando como $O(\log N)$.
- **Espacio**: $O(1)$ -> La solución opera puramente en el dominio numérico sin convertir el entero a cadena. Solo se emplea una variable escalar adicional `revertedNumber` para acumular la mitad revertida del número. No se crean cadenas, arreglos ni estructuras de datos de tamaño variable, garantizando un consumo de memoria constante.

## Intuición y Enfoque

El problema solicita determinar si un entero es un palíndromo (se lee igual de izquierda a derecha y de derecha a izquierda) **sin convertirlo a cadena**.

Un enfoque naïve convertiría `x` a su representación en cadena y usaría dos punteros o una reversión para verificar la simetría. Aunque es $O(\log N)$ en tiempo, implica una conversión a string con asignación de memoria dinámica ($O(\log N)$ de espacio), lo cual viola el espíritu del problema.

La solución implementa la **Reversión de la Mitad Posterior del Número (Half-Number Reversal)**, que es significativamente más elegante:

**Optimización de las Guardas Iniciales**: Antes del bucle se aplican dos descartaciones lógicas en $O(1)$:

1. Si `x < 0`: los negativos nunca son palíndromos (el signo `-` no puede aparecer al final).
2. Si `x % 10 === 0 && x !== 0`: cualquier número positivo que termine en `0` no puede ser palíndromo, ya que la representación correspondiente tendría que empezar con `0`, lo cual no es válido para enteros positivos. El cero mismo (`x === 0`) es la excepción y sí es palíndromo.

**Reversión de Solo la Mitad**:

1. En lugar de revertir el número completo (que podría causar desbordamiento y requeriría verificar contra el número original completo), el bucle remueve dígitos del extremo derecho de `x` y los acumula en `revertedNumber` hasta que `revertedNumber >= x`.
2. Cuando `x <= revertedNumber`, exactamente la mitad de los dígitos ha migrado de `x` a `revertedNumber`. En este punto, la mitad izquierda del número original es `x` y la mitad derecha revertida es `revertedNumber`.
3. **Verificación Dual**:
   - `x === revertedNumber`: para números con un número **par** de dígitos, ambas mitades deben ser iguales.
   - `x === Math.floor(revertedNumber / 10)`: para números con un número **impar** de dígitos, el dígito central está en la posición más significativa de `revertedNumber`. Al dividirlo por 10, se descarta dicho dígito central y se compara la mitad izquierda con la mitad derecha sin el centro.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Guarda con `&&` de Cortocircuito**: La condición `x % 10 === 0 && x !== 0` usa evaluación de cortocircuito. Si `x % 10 !== 0`, la segunda subexpresión `x !== 0` no se evalúa. El orden es semánticamente correcto: primero verificamos el caso general (último dígito es cero) y luego excluimos la excepción (el cero mismo).
  - **`Math.floor(x / 10)` y `Math.floor(revertedNumber / 10)`**: Se usa `Math.floor()` para la división entera en dos lugares: al reducir `x` en el bucle y al descartar el dígito central en la verificación final. Ambos usos son obligatorios por el modelo numérico de punto flotante de JavaScript.
  - **Mutación Directa de `x`**: El parámetro `x` se modifica directamente dentro de la función (`x = Math.floor(x / 10)`). En JavaScript, los tipos primitivos se pasan por valor, por lo que esta mutación no afecta al valor original del llamador. Esta es una elección deliberada que evita la declaración de una variable `left` o `original` adicional.

- **PHP**:
  - **Comparación Estricta (`===`) en las Guardas**: La condición `$x % 10 === 0 && $x !== 0` usa el operador de identidad estricta en ambas comparaciones. En PHP, `===` compara valor **y tipo**, previniendo coerciones donde `0 === false` (falso con `===` pero `true` con `==`). Esto es especialmente importante para la comparación `$x !== 0`.
  - **División Entera con Cast `(int)`**: PHP usa `(int)($x / 10)` tanto en el cuerpo del bucle como en la verificación final `(int)($revertedNumber / 10)`. El operador `/` de PHP retorna `float` para divisiones no exactas, y el cast `(int)` trunca el resultado al entero correcto. La alternativa nativa `intdiv()` (PHP 7+) sería equivalente y más idiomática semánticamente.
  - **Isomorfismo Estructural Total**: La lógica de ambas soluciones es matemáticamente idéntica. La diferencia entre `Math.floor(revertedNumber / 10)` (JS) y `(int)($revertedNumber / 10)` (PHP) es puramente de mecanismo de división entera, no de algoritmo.

## Lecciones Clave

- **Procesamiento de la Mitad para Evitar Desbordamiento y Reducir Comparaciones**: Revertir solo la mitad posterior del número en lugar del número completo es una optimización que simultáneamente evita el potencial desbordamiento de enteros al construir el número revertido completo y reduce el número de iteraciones a la mitad. Este principio de "procesar solo lo necesario" (en este caso, la mitad) y comparar las dos mitades al final es directamente análogo al enfoque de Two Pointers para verificar palíndromos en cadenas: un puntero desde el inicio y otro desde el final, avanzando hacia el centro.
- **Descarte Temprano mediante Propiedades Estructurales Matemáticas**: Las guardas iniciales (negativos y múltiplos de 10) son el resultado de analizar las **propiedades matemáticas estructurales** del problema antes de ejecutar cualquier cómputo. Identificar y descartar en $O(1)$ las clases de inputs que nunca pueden satisfacer la condición es una técnica de diseño de algoritmos de primer nivel que puede reducir dramáticamente el espacio de búsqueda efectivo en cualquier función de validación o clasificación.
