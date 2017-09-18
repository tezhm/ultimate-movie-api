<?php

/**
 * @SWG\SecurityScheme(
 *   securityDefinition="api_key",
 *   type="apiKey",
 *   in="header",
 *   name="api_key"
 * )
 */

/**
 * @SWG\SecurityScheme(
 *   securityDefinition="uma_auth",
 *   type="oauth2",
 *   authorizationUrl="http://localhost:8000/login",
 *   flow="implicit",
 *   scopes={
 *     "read:movies": "read movies",
 *     "write:movies": "modify movies",
 *     "read:genres": "read genres",
 *     "write:genres": "modify genres",
 *     "read:actors": "read actors",
 *     "write:actors": "modify actors"
 *   }
 * )
 */
