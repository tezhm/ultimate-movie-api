import requests

#r = requests.post("http://127.0.0.1/user/create", data={'username': 'fred', 'password': 'password123'})
#print(r.status_code, r.reason)
#print(r.text[:300] + '...')


r = requests.post("http://localhost/login", data={'username': 'fred', 'password': 'password123'})
print(r.status_code, r.reason)
print(r.text[:300] + '...')



# $2y$10$.wFMiowqZEZPs1LeGC423ux62ti.LXvUoFsrCovUpaGIZOSqA2TYC
