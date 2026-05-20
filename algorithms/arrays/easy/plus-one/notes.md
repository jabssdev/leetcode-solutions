# Plus One

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud del arreglo `digits` -> En el peor de los casos (cuando todos los dígitos son `9`, por ejemplo `[9, 9, 9]`), el bucle `for` recorre las $N$ posiciones del arreglo de derecha a izquierda, asignando `0` a cada una. Posteriormente, la operación de inserción al inicio (`unshift` / `array_unshift`) tiene un coste de $O(N)$ al desplazar todos los elementos una posición. En el mejor de los casos (cuando el último dígito es menor que `9`), el algoritmo se ejecuta en tiempo constante $O(1)$ gracias al retorno temprano inmediato tras el incremento.
- **Espacio**: $O(1)$ -> La modificación se realiza **in-place** directamente sobre el arreglo de entrada. No se crean estructuras de datos auxiliares, mapas ni arreglos temporales. La única excepción es el caso extremo de desbordamiento total (todos los dígitos son `9`), donde se inserta un único elemento `1` al inicio, incrementando el tamaño del arreglo en una unidad. Este crecimiento es inherente al resultado del problema y no constituye espacio auxiliar de cómputo.

## Intuición y Enfoque

El problema simula la operación aritmética elemental de sumar `1` a un número entero representado como un arreglo de dígitos individuales. La clave del algoritmo reside en la **propagación del acarreo (carry propagation)** aplicada de derecha a izquierda, tal como lo haríamos manualmente en una suma escolar.

En lugar de convertir el arreglo a un número entero (lo cual introduciría problemas de desbordamiento con números arbitrariamente grandes que excedan los límites de `Number.MAX_SAFE_INTEGER` en JavaScript o `PHP_INT_MAX` en PHP), el enfoque opera dígito a dígito con una lógica extremadamente elegante de **cortocircuito temprano**:

1. Iteramos desde el dígito menos significativo (último elemento) hacia el más significativo (primero).
2. Si el dígito actual es menor que `9`, simplemente lo incrementamos en `1` y retornamos inmediatamente. No hay acarreo que propagar, por lo que la operación está completa.
3. Si el dígito actual es `9`, al sumarle `1` se convierte en `10`. En lugar de manejar un acarreo explícito, asignamos `0` al dígito actual y dejamos que la siguiente iteración del bucle actúe como la propagación implícita del acarreo al dígito adyacente.
4. Si el bucle termina sin haber retornado (es decir, todos los dígitos eran `9` y todos fueron convertidos a `0`), significa que el número resultante requiere un dígito adicional. En este caso, insertamos un `1` al inicio del arreglo (por ejemplo, `[9, 9, 9]` → `[1, 0, 0, 0]`).

Esta técnica elimina la necesidad de una variable de acarreo explícita (`carry`), resultando en un código minimalista y altamente legible.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **Longitud Implícita**: Se accede a la propiedad `.length` directamente en la inicialización del bucle `for (let i = digits.length - 1; ...)` sin necesidad de almacenarla en una variable separada, dado que la longitud del arreglo solo cambia fuera del cuerpo del bucle.
  - **`Array.prototype.unshift()`**: Para el caso de desbordamiento total, se utiliza el método nativo `unshift(1)` que inserta el elemento `1` al inicio del arreglo, desplazando internamente todos los elementos existentes una posición hacia la derecha. Esta operación tiene un coste de $O(N)$ en motores como V8 al requerir una reindexación completa de los elementos.
  - **Mutación In-Place**: El arreglo original es modificado directamente (tanto el incremento como la inserción con `unshift`) y retornado, cumpliendo con la expectativa de eficiencia de memoria del problema.

- **PHP**:
  - **Precálculo de Longitud**: La longitud del arreglo se precalcula en `$n = count($digits)` antes del bucle. Aunque la función `count()` es de tiempo constante $O(1)$ en PHP (ya que los arreglos de PHP almacenan internamente su tamaño como metadato en la estructura `zend_array`), esta práctica es idiomática en PHP y evita re-evaluaciones innecesarias en la firma del `for`.
  - **`array_unshift()`**: El equivalente funcional en PHP es `array_unshift($digits, 1)`, una función nativa del núcleo de PHP que inserta uno o más valores al principio del arreglo y reindexar automáticamente todas las claves numéricas existentes. Al igual que en JavaScript, su coste temporal es $O(N)$ debido a la necesidad de reorganizar la tabla hash interna.
  - **Paso por Valor (Semántica Copy-on-Write)**: A diferencia de la versión en JS, la firma de la función PHP **no** usa el operador de referencia `&`. En este caso no es necesario porque el resultado se devuelve explícitamente con `return $digits`, siendo el arreglo retornado la fuente de verdad para el llamador.

## Lecciones Clave

- **Propagación de Acarreo Implícita por Diseño de Flujo**: En lugar de gestionar estados intermedios mediante variables auxiliares de acarreo (`carry`), es posible diseñar el flujo de control del algoritmo de forma que el comportamiento natural del bucle y los retornos tempranos representen la propagación del acarreo de forma implícita. Cuando un dígito absorbe el incremento (`< 9`), el retorno corta la propagación; cuando no puede (`=== 9`), la iteración continúa al siguiente dígito, simulando la cascada.
- **Aritmética de Precisión Arbitraria a Nivel de Dígito**: Este patrón de operar dígito a dígito sobre una representación en arreglo es la base de la **aritmética de precisión arbitraria (Big Integer Arithmetic)**. Debe aplicarse siempre que trabajemos con números que excedan los límites nativos de los tipos numéricos del lenguaje, como en criptografía, procesamiento financiero de alta precisión, o problemas algorítmicos con restricciones de magnitud numérica extrema.
