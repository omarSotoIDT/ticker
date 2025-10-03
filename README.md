# idt-template-monorepo
Repositorio template para la creación de nuevos proyectos con estructura monorepo.

Este repositorio define una estructura monorepositorio e incluye configuraciones importantes para `semantic-release` en un entorno monorepo, además de workflows de despliegue con GitHub Actions.

### Características principales
- Estructura en formato monorepositorio con directorios y archivos de configuración.
- Configuración de `semantic-release` para monorepo que automatiza el cálculo de versiones.
- Workflows para despliegue mediante GitHub Actions.
- Ramas principales: `main`, `stage` y `develop`.

---

## Acerca de este repositorio

- Los nuevos repositorios de proyectos con estructura monorepositorio deben crearse a partir de este template. GitHub permite la creación de repositorios desde una plantilla: [Guía de GitHub](https://docs.github.com/es/repositories/creating-and-managing-repositories/creating-a-repository-from-a-template).

---

## Configuración de `semantic-release` para monorepo

- Todos los commits deben seguir las convenciones de "Versionado Semántico".
- `semantic-release` genera tags y releases en las ramas `main` y `stage`.
- Los archivos `/package.json` y `/package-lock.json` ya incluyen las dependencias necesarias para instalar `semantic-release`.
- Las librerías de `semantic-release` fueron instaladas y probadas con `node@23.0.0` y `npm@10.9.0`.

### Estructura del proyecto

- Las aplicaciones en el nuevo monorepo deben estar en la carpeta `/apps`.
- Las carpetas `/apps/project1` y `/apps/project2` son ejemplos de estructura y configuración para los proyectos que contendrá el nuevo monorepo.
- Cada proyecto debe ser nombrado de acuerdo a su tipo y propósito. Ejemplos: `api`, `app-corporativo`, `app-unidad`, `webapp-admin`, `frontend-cliente`, `desktop-tickets`.

### Tipos de proyectos

Cada tipo de proyecto debe ser definido de la siguiente manera:

| Tipo      | Descripción                                                                                     |
|-----------|-------------------------------------------------------------------------------------------------|
| `api`     | Para el API del proyecto. Generalmente solo hay un API por proyecto, denominado simplemente "api". |
| `app`     | Para aplicaciones móviles.                                                                      |
| `webapp`  | Para proyectos monolíticos que manejan lógica de frontend y backend.                            |
| `frontend`| Para proyectos del lado del cliente, que usualmente hacen peticiones al API, como SPA's.        |
| `desktop` | Para aplicaciones de escritorio. Ejemplo: gestión de tickets o etiquetas.                       |

### Estructura de carpetas

- El código fuente de cada aplicación debe ubicarse en `/apps/project1/src`, donde `project1` representa el nombre del proyecto.
- La carpeta `/apps/project1/docker` debe contener los archivos necesarios para la construcción de la imagen Docker (ejemplo: `000-default.conf`).
- El archivo `/apps/project1/Dockerfile` debe actualizarse según las necesidades de cada aplicación.

### Requisitos para despliegue con GitHub Actions

Para proyectos que serán desplegados mediante workflows en GitHub Actions, ten en cuenta los siguientes puntos:

1. Cada proyecto dentro de `/apps` debe tener un archivo `package.json` en el directorio `/src`. Ejemplo: `/apps/project1/src/package.json`.
2. El archivo `/apps/project1/src/package.json` debe incluir la propiedad `name` con un valor que corresponda al nombre del proyecto/directorio. Ejemplo: `"name": "frontend-cliente"`.
3. Cada proyecto debe tener un archivo `.releaserc` dentro del directorio `/src`. Ejemplo: `/apps/project1/src/.releaserc`.
4. El archivo `.releaserc` debe actualizarse en la propiedad `tagFormat` para que refleje el nombre del proyecto. Esta propiedad se usará para crear tags de versionamiento y nombrar la imagen Docker, por lo que debe ser clara y consistente.

---

## Configuración del workflow para GitHub Actions

- El workflow está configurado en el archivo `/.github/workflows/build-push-dockerhub.yml`.
- Incluye pasos para generar versiones y agregar nuevas imágenes Docker a DockerHub.
- El worfkow es ejecutado al realizar merge sobre las ramas `main` o `stage`.
- El workflow ejecuta estos pasos únicamente para los proyectos especificados en la matriz `build_and_push/strategy/matrix/project`. Por lo tanto, esta matriz debe contener todos los proyectos que serán desplegados a través de GitHub Actions.
- El prefijo `idt-template` en la propiedad `Build and push Docker image/with/tags` debe actualizarse según el nombre del nuevo proyecto que se creará a partir de este template.

---

## Configuración de ramas

Después de clonar el proyecto desde este template, es necesario crear las ramas `stage` y `develop`, ya que estas no se copian automáticamente desde el template.

---

## Referencia de proyectos existentes

A la fecha de este README, los proyectos **Keymaster** y **Vesself** han sido migrados a una estructura monorepo. En caso de dudas o para mayor referencia, estos proyectos pueden ser consultados como ejemplos de implementación.

---

> **Nota**: Este archivo README describe las consideraciones más relevantes al crear un nuevo proyecto desde este template y **debe eliminarse del nuevo proyecto tras su creación.**.

**Última actualización:** 04 de noviembre de 2024.

