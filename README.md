## Via le service Web :
1. Accès à swagger via [ce lien](http://185.98.138.56/api/documentation)

## Via POSTMAN :

### Pré-requis
1. Se connecter sur Postman

### S'enregistrer
1. Créer une requête **POST**.
2. Renseigner l'adresse suivante : "**http://185.98.138.56/api/auth/register**"
3. Dans le **Headers**, mettre les **Key / Value** suivantes :
   - Accept | application/json
   - Content-Type | application/json
4. Dans **Body** en **raw**, sélectionner **JSON** puis renseigner le json de la manière suivante :
```
{
    "email": "MonMail@example.com",
    "name": "MonNom",
    "password": "MonMotDePasse",
    "profiles_id": 1
}
```
*1 = Super Admin, 2 = Admin, 3 = Utilisateur Premium, 4 = Utilisateur*
5. Appuyer sur **Send**.

### Se connecter

Sur Postman, suivre les étapes suivantes :

1. Créer une requête **POST**.
2. Renseigner l'adresse suivante : "**http://185.98.138.56/api/auth/login**"
3. Dans le **Headers**, mettre les **Key / Value** suivantes :
   - Accept | application/json
   - Content-Type | application/json
4. Dans **Body** en **raw**, sélectionner **JSON** puis renseigner le json de la manière suivante :
```
{
    "email": "test@example.com",
    "password": "password"
}
```
5. Créer une nouvelle variable d'environnement et nommer la variable "bearerToken".
6. Dans **Authorization**, sélectionner *Inherit auth from parent*.
7. Dans **Scripts** puis **Post-response**, renseigner :
```
var jsonData = pm.response.json();
pm.environment.set("bearerToken", jsonData.access_token);
```
8. Appuyer sur **Send**.
9.  Pour vérifier que notre **access_token** soit bien fonctionnel, on pourra faire une autre requête **POST** sur l'adresse suivante : "**http://185.98.138.56/api/auth/me**" avec les valeurs suivantes :
   - Accept | application/json
   - Content-Type | application/json
   - Dans **Authorization**, sélectionner *Bearer Token* et dans **Token**, renseigner :
```
{{bearerToken}}
```

Si tout est bon, cela affichera les informations de l'utilisateur.

### Gestion du profil

1. Changer de profil : 
   1. Se connecter
   2. Renseigner l'adresse suivante : "**http://185.98.138.56/api/auth/changeprofile**"
   3. Renseigner le json suivant :
   ```
   {
      "profile_name": "Admin"
   }
   ```
   *Les différents profils sont : **Super Admin**, **Admin**, **Utilisateur Premium** et **Utilisateur**.
2. Mettre à jour les droits d'un profil : 
   1. Se connecter
   2. Renseigner l'adresse suivante : "**http://185.98.138.56/api/auth/changerights**"
   3. Renseigner le json suivant :
   ```
   {
      "profile_name" : "Utilisateur Premium",
      "right": "canUseIsMailExist",
      "isActivate": true
   }
   ```
3.

