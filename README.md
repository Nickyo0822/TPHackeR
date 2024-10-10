### S'enregistrer
1. Créer une requête **POST**.
2. Renseigner l'adresse suivante : "**http://127.0.0.1:8000/api/auth/register**"
3. Dans le **Headers**, mettre les **Key / Value** suivantes :
   - Accept | application/json
   - Content-Type | application/json
4. Dans **Body** en **raw**, sélectionner **JSON** puis renseigner le json suivant :
```
{
    "email": "MonMail@example.com",
    "name": "MonNom",
    "password": "MonMotDePasse"
}
```
5. Appuyer sur **Send**.

### Se connecter

Sur Postman, suivre les étapes suivantes :

1. Créer une requête **POST**.
2. Renseigner l'adresse suivante : "**http://127.0.0.1:8000/api/auth/login**"
3. Dans le **Headers**, mettre les **Key / Value** suivantes :
   - Accept | application/json
   - Content-Type | application/json
4. Dans **Body** en **raw**, sélectionner **JSON** puis renseigner le json suivant :
```
{
    "email": "test@example.com",
    "password": "password"
}
```
5. Appuyer sur **Send**.
6. Sauvegarder l'**access_token** dans un bloc-note (valable 1h).
7. Pour vérifier que notre **access_token** soit bien fonctionnel, on pourra faire une autre requête **POST** sur l'adresse suivante : "**http://127.0.0.1:8000/api/auth/me**" avec les valeurs suivantes :
   - Accept | application/json
   - Content-Type | application/json
   - Authorization | Bearer ***Coller l'access_token ici***

Si tout est bon, cela affichera les informations de l'utilisateur.