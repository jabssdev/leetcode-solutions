# Valid Anagram — Technical Notes

## Análisis de Complejidad

- **Tiempo: `O(n)`** — Ambas soluciones realizan un número constante de pasadas lineales sobre las cadenas de entrada de longitud `n`:
  - **JS:** Dos bucles secuenciales — uno sobre `s` para construir el mapa de frecuencias, y uno sobre `t` para decrementar y validar. Total: `2n` operaciones → `O(n)`.
  - **PHP:** Un único bucle `for` que procesa `s` y `t` simultáneamente índice a índice (incrementando para `s`, decrementando para `t`), seguido de un `foreach` de verificación. Total: `n + n` operaciones → `O(n)`.

  El guard clause `length !== length` en ambas versiones descarta en `O(1)` el caso más común de no-anagramas antes de cualquier procesamiento.

- **Espacio: `O(k)`** — donde `k` es el tamaño del alfabeto de caracteres únicos en la entrada. En el caso del enunciado (solo letras minúsculas del inglés), `k ≤ 26`, lo que hace el espacio efectivamente `O(1)`. Para Unicode arbitrario (caso de la solución PHP con `mb_*`), `k` puede ser mayor pero sigue siendo acotado por el número de caracteres únicos, no por `n`. La estructura auxiliar es el `Map` (JS) o el array asociativo `$counts` (PHP).

---

## Intuición y Enfoque

La técnica utilizada es **Frequency Counter con Hash Map** — una de las aplicaciones más directas y eficientes del patrón de conteo de frecuencias para verificar equivalencia de multiconjuntos.

**Premisa clave:** Dos cadenas son anagramas si y solo si son **permutaciones una de la otra**, es decir, contienen exactamente los mismos caracteres con exactamente las mismas frecuencias. Esto es equivalente a verificar que sus **multiconjuntos de caracteres son iguales**.

**Estrategia JS — dos pasadas con early exit:**

```
Pasada 1 (sobre s): construir mapa de frecuencias positivas.
  countMap: { 'a': 2, 'n': 1, 'r': 1, 'g': 1, 'm': 1 }  ← para "anagram"

Pasada 2 (sobre t): decrementar y validar en cada paso.
  - Si el char no existe en el mapa → retornar false inmediatamente.
  - Si la frecuencia cae a negativo → retornar false inmediatamente.
```

El early exit en la segunda pasada permite terminar en `O(1)` tan pronto como se detecta una discrepancia, sin necesidad de procesar el resto de `t`.

**Estrategia PHP — una pasada con balance neto:**

```
Un solo bucle: incrementar $counts[s[i]], decrementar $counts[t[i]].
Al finalizar: si todos los valores en $counts son 0, es anagrama.
```

Esta variante es más compacta y elimina la necesidad de verificar existencia de clave durante el procesamiento, pero **no tiene early exit** durante el bucle principal — siempre procesa los `n` caracteres completos antes de verificar.

**¿Por qué es óptima frente a la fuerza bruta?** La alternativa naive sería ordenar ambas cadenas y compararlas: `O(n log n)` tiempo. El Frequency Counter logra `O(n)` usando espacio `O(k)` para evitar el ordenamiento.

---

## Notas Políglotas (JavaScript vs PHP)

- **JavaScript:** Utiliza `Map` nativo de ES6 en lugar de un objeto literal `{}`. La diferencia es semántica: `Map` garantiza que las claves son tratadas estrictamente como strings (o cualquier tipo), evita colisiones con propiedades heredadas del prototype (`hasOwnProperty`, `constructor`, etc.), y provee `has()` para verificar existencia sin ambigüedad. El patrón `(countMap.get(char) || 0) + 1` usa el cortocircuito de `||` para manejar la ausencia de clave — si `get` retorna `undefined` (falsy), se usa `0` como valor base. La iteración usa `for...of` sobre la cadena directamente, que en JS itera correctamente sobre **code points Unicode** (no bytes ni code units), manejando correctamente caracteres fuera del BMP como emojis.

- **PHP:** La solución demuestra una conciencia explícita de **internacionalización (i18n)** al usar `mb_strlen()` y `mb_str_split()` en lugar de sus equivalentes de byte `strlen()` y `str_split()`. Las funciones `mb_*` (Multibyte String) operan sobre **caracteres Unicode** en lugar de bytes, lo que hace esta solución correcta para cadenas UTF-8 con caracteres multibyte (acentos, caracteres asiáticos, etc.). `mb_str_split($s)` devuelve un array de caracteres Unicode, haciendo el acceso por índice `$sChars[$i]` seguro. El operador null coalescing `??` (`$counts[$key] ?? 0`) es la forma idiomática de PHP 7+ para acceso seguro a claves inexistentes en arrays asociativos, equivalente al `|| 0` de JS pero sin la ambigüedad del cortocircuito con valores falsy como `0` o `""`.

> [!IMPORTANT]
> La elección de `mb_strlen` vs `strlen` en PHP no es cosmética — para una cadena UTF-8 como `"café"`, `strlen()` retornaría `5` (bytes) mientras que `mb_strlen()` retornaría `4` (caracteres). Usar `strlen` con caracteres multibyte produciría resultados de longitud incorrectos y potencialmente falsos negativos en el guard clause. La solución PHP es internacionalmente correcta; la solución JS lo es por diseño del lenguaje.

> [!NOTE]
> La estrategia de PHP (balance neto en una sola pasada) es arquitectónicamente más elegante para el caso sin early exit, ya que evita la doble verificación de existencia + decremento que requiere JS. Sin embargo, JS compensa con early exit en la segunda pasada, lo que puede ser significativamente más rápido en la práctica para cadenas largas con discrepancias tempranas.

---

## Lecciones Clave

- **Frequency Counter como reemplazo de `O(n log n)` con ordenamiento:** Siempre que un problema requiera verificar si dos colecciones son permutaciones, tienen los mismos elementos, o comparten una distribución de frecuencias, el Frequency Counter con Hash Map es la herramienta de referencia. Transforma el problema de comparación de orden en un problema de comparación de multiconjuntos en `O(n)`. Aplicar en: detección de anagramas, verificación de permutaciones, _Group Anagrams_, _Find All Anagrams in a String_, balanceo de paréntesis ponderado, o cualquier problema de equivalencia entre secuencias.

- **`??` vs `||` para valores por defecto — la trampa del falsy:** En PHP, `$map[$key] ?? 0` es semánticamente más correcto que `$map[$key] || 0` cuando el valor puede ser `0` o `""` (falsy pero válido). En JS, `map.get(key) || 0` funciona correctamente aquí porque `undefined` (ausencia de clave) es falsy y los contadores nunca deberían ser `0` en la primera pasada — pero en otros contextos donde `0` es un valor legítimo, usar `map.get(key) ?? 0` (nullish coalescing de ES2020) es la forma correcta. Preferir siempre el operador de coalescencia nula (`??`) sobre el de cortocircuito lógico (`||`) cuando el valor por defecto debe aplicarse solo para `null`/`undefined`, no para cualquier falsy.
