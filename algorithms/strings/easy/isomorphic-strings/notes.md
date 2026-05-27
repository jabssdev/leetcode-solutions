# Isomorphic Strings

## Análisis de Complejidad

- **Tiempo**: $O(N)$ donde $N$ es la longitud de las cadenas `s` y `t` (que por definición del problema tienen la misma longitud) -> El bucle `for` recorre exactamente $N$ posiciones. En cada iteración, la obtención del código de carácter y las operaciones de lectura/escritura sobre los arreglos de tamaño fijo son todas de tiempo constante $O(1)$. No hay bucles anidados ni llamadas recursivas.
- **Espacio**: $O(1)$ -> Aunque se crean dos arreglos `sMap` y `tMap`, ambos tienen tamaño fijo de **128 entradas** (el número de puntos de código ASCII), independientemente de la longitud o el contenido de las cadenas de entrada. Este espacio es una constante del algoritmo, no una función del tamaño de la entrada. La solución utiliza arreglos de tamaño fijo como tablas de lookup de rendimiento superior a un `Map` o diccionario dinámico.

## Intuición y Enfoque

El problema solicita determinar si dos cadenas `s` y `t` son **isomorfas**: existe una función de mapeo biyectivo entre sus caracteres tal que reemplazar cada carácter en `s` por su correspondiente en `t` produce `t` exactamente. La condición de isomorfismo requiere que la correspondencia sea **bidireccional y consistente**: si `s[i] → t[i]`, entonces ningún otro carácter de `s` puede mapear a `t[i]`, y `s[i]` no puede mapear a ningún otro carácter de `t`.

Un enfoque ingenuo usaría dos `Map` o diccionarios para registrar el mapeo bidireccional, con verificaciones en cada posición. La solución implementa una optimización más elegante con **arreglos de tamaño fijo como tablas de índice de última visita**.

El **invariante clave** del algoritmo: dos caracteres `s[i]` y `t[i]` son consistentes con el mapeo isomórfico si y solo si la **última vez que `s[i]` fue visto es la misma que la última vez que `t[i]` fue visto**. Si en algún momento `sMap[s[i]] ≠ tMap[t[i]]`, existe una inconsistencia que viola el isomorfismo.

El mecanismo concreto:

1. Se inicializan dos arreglos de 128 ceros: `sMap` y `tMap`, indexados por el código ASCII del carácter.
2. Por cada posición `i`, se obtienen los códigos `sVal` y `tVal`.
3. Se verifica `sMap[sVal] !== tMap[tVal]`: si los dos caracteres no tienen el mismo "marcador de última visita", el mapeo es inconsistente → `return false`.
4. Se actualiza `sMap[sVal] = i + 1` y `tMap[tVal] = i + 1` (se usa `i + 1` en lugar de `i` para distinguir la posición `0` del valor inicial `0` que representa "nunca visitado").
5. Si el bucle termina sin inconsistencias, `return true`.

**Por qué `i + 1` y no `i`**: El índice `i` comienza en `0`, y los arreglos están inicializados a `0`. Usar `i` directamente haría que la posición `0` sea indistinguible del valor inicial "nunca visitado". Al usar `i + 1` (que comienza en `1`), se garantiza que `0` siempre significa "aún no visto" y cualquier valor `≥ 1` indica la última posición procesada más uno.

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript**:
  - **`new Array(128).fill(0)`**: Se crea un arreglo denso de 128 elementos inicializados a `0` usando el constructor `Array` con `.fill()`. Esta es la forma idiomática en JavaScript para crear tablas de lookup de tamaño fijo con un valor predeterminado. Un arreglo creado con `new Array(128)` sin `.fill()` sería un arreglo disperso (sparse array) cuyos índices no inicializados son `undefined`, lo que rompería la comparación `sMap[sVal] !== tMap[tVal]` ya que `undefined !== undefined` es `false`, pero `undefined !== 0` podría generar comportamientos inesperados al mezclar estados.
  - **`String.prototype.charCodeAt(i)`**: Se usa el método nativo `.charCodeAt(i)` para obtener el código Unicode (ASCII para los caracteres de este problema) del carácter en la posición `i`. Esto produce un entero en el rango `[0, 127]` para caracteres ASCII, que se usa directamente como índice del arreglo.
  - **Arreglos vs. `Map`**: El uso de `Array(128)` en lugar de `new Map()` es una optimización de rendimiento deliberada. El acceso a un arreglo por índice entero es $O(1)$ garantizado por el motor JavaScript (V8 optimiza arreglos densos como arrays contiguos en memoria), mientras que un `Map` tiene una constante de tiempo mayor por su overhead de hashing y almacenamiento de pares clave-valor.

- **PHP**:
  - **`array_fill(0, 128, 0)`**: PHP usa la función nativa `array_fill(start_index, count, value)` para crear un arreglo PHP de 128 elementos inicializados a `0`, con índices del `0` al `127`. Es el equivalente semántico exacto de `new Array(128).fill(0)` en JavaScript.
  - **`ord($s[$i])`**: PHP utiliza la función nativa `ord()` para obtener el valor ASCII de un carácter, equivalente a `.charCodeAt(i)` de JavaScript. El acceso `$s[$i]` retorna el byte en la posición `$i` de la cadena, y `ord()` lo convierte al entero correspondiente en `[0, 127]` para caracteres ASCII.
  - **Comparación Estricta (`!==`)**: La condición `$sMap[$sVal] !== $tMap[$tVal]` usa el operador de identidad estricta, previniendo coerciones implícitas de tipos en PHP. Esto es especialmente importante ya que los arreglos PHP devuelven el tipo exacto almacenado, y la coerción débil podría generar falsos negativos si los valores almacenados fueran de tipos mixtos.
  - **Isomorfismo Algorítmico Total**: La solución PHP es un espejo exacto de la versión JavaScript en términos de lógica y estructura. Las diferencias se limitan a: `charCodeAt(i)` vs `ord($s[$i])`, `new Array(128).fill(0)` vs `array_fill(0, 128, 0)`, y las convenciones sintácticas habituales.

## Lecciones Clave

- **Arreglos de Tamaño Fijo como Tablas de Índice de Estado: la Alternativa $O(1)$ a los Hash Maps**: Cuando el dominio de las claves es un conjunto finito y acotado (como los 128 o 256 caracteres ASCII, o los 26 caracteres del alfabeto inglés), un arreglo de tamaño fijo indexado por el valor del carácter es una alternativa superior a un `Map` o diccionario: misma complejidad asintótica $O(1)$ por acceso, pero con menor constante de tiempo (sin overhead de hashing) y uso de caché de CPU más eficiente (localidad de memoria contigua). Este patrón es aplicable en _Word Pattern_, _Valid Anagram_, _Ransom Note_, _Character Replacement_, y cualquier problema de procesamiento de strings sobre alfabetos acotados.
- **El Patrón de Última Visita Sincronizada como Verificador de Biyección**: La técnica de almacenar el **índice de la última visita** (en lugar de un mapeo directo del carácter destino) en dos tablas paralelas y verificar su igualdad es un patrón poderoso para validar correspondencias biyectivas sin necesidad de dos mapas de dirección cruzada. El uso de `i + 1` como valor de marca (en lugar de `i`) para distinguir "nunca visto" de "visto en la posición 0" es un detalle de implementación sutil pero crítico que evita el error de _off-by-one_ en la detección de la primera posición.
