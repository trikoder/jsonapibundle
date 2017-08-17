# Request flow in jsonapi

Any request received by symfony is intercepted by jsonapi request listener for any controller that is marked as jsonapi controller.
Listener transforms and decodes request and provides HTTP-like request to controller action.
