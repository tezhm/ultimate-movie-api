# Improvements

Following is a list of TODOs and alternate implementations to improve scalability and performance.

## Queries

Queries at the moment are hydrating entities via the doctrine ORM. This introduces overhead from parsing doctrine mappings, using reflection to hydrate objects, and spinning up new object instances on the heap. This can be avoided by implementing a more CQRS style architecture. Queries could be run through repositories which avoid the write domain by using DQL/SQL to query values. This naturally enables the read path to cross aggregates as read operations do not alter the state of the domain.

## Aggregate segregation

The domain has been implemented as a single aggregate where movie is the root. This however could be divided into multiple aggregates because Actor, Movie, and Genre can qualify as aggregate roots. For instance, a Genre can exist without a Movie - as with Actors.

Segregating the entities into their own aggregates would allow for a more scalable design. Movie instances would not have hard dependencies on Genres and Actors. Instead, the relations would be satisfied by sharing UUIDs. This would allow Genre and Actor instances to be added to a Movie without actually existing yet, pushing for a more eventually consistent design.

This would however introduce more complexities in design. For example, Genre includes Actors from both Actors directly added to Genre and Actors within Movies added to Genre. To achieve this without breaking aggregate boundaries, an extra service would be required to keep the Genre consistent with its added Movies. The service could be triggered from events fired from Movie.

## Caching

Database access is expensive and at the moment, every command and query accesses the database. This can be reduced by using caching technologies such as redis or memcache to store query results. The cached results can be returned if a query is repeated within the cache results expiration.

Another expense involved with queries is the transmission of data when results have not changed. The use of `etag` headers to identify the last query result when querying can prevent unchanged results from repeat transmission. This can also be applied to static content such as images which rarely change.

## Handling images

Images at the moment have poor validation (only validate the size) and are not normalised. As there are many interfaces which view images, from mobiles to 4k monitors, the images should be optimised to match the varying resolutions.

Normalising images for thumbnails should be a write path operation. The image should be stripped of meta-data, resized to x dimensions, compressed and stored. This includes all other image sizes required by the query side.

## Security

A large pitfall with the current authentication service is token life. At the moment, a generated token lasts indefinitely. Tokens should expire after an arbitrary time (based on activity or some fixed time period) to prevent users from having access forever after logging in once.

## Rate throttling

This is more of an implementation detail than a design issue, but at the moment rate throttling is via an array cache. Thus, PHP instances are not actually sharing the current API call rates. This should be changed to use a shared cache such as redis, memcache, or even a database backing (would be considerably slower).
