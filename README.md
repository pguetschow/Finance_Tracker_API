# Finanztracker

Übersicht über Fixkosten (Einnahmen/Ausgaben)

Ein Nutzer kann wiederkehrende Einnahmen und Ausgaben in der Anwendung registrieren.

### user

- id
- firstName
- lastName
- email
- phone
- password
- salt
- active


Eine einmalige Einnahme/Ausgabe muss einer Kategorie zugeordnet werden.

### entry

- id
- name
- category
- amount
- date

Eine Vertrag muss einer Kategorie zugeordnet werden und kann um ein Dokument ergänzt werden.

### contract

- id
- name
- category
- amount
- startDate
- endDate
- interval (monatlich, quartalsweise, halbjährlich, jährlich)
- filename

# Views

- Login
- Registrierung
- Passwort Vergessen
- Aktivierung
- Nutzer bearbeiten

#### Dashboard:

- Aktueller Zeitraum (Monat) als Datum mit Slidern nach rechts/links
- Aktueller Finanzstand
- Voraussichtlich verbleibendes Geld
- Graph mit Monatsfinanzentwicklung
- Diagramm mit Einnahmen nach Kategorie
- Diagramm mit Ausgaben nach Kategorie
- Wenn Verträge bald enden: anzeigen mit Enddatum
- Intervalle als Tabs


# Authentifizierung

Um die Sicherheit zu erhöhen, wurde der API Token Ansatz verworfen und stattdesse n OAuth2 implementiert.

Zuerst muss ein Access Token erzeugt werden. Dafür müssen die Client- & User-Credentials als Parameter and den Tokenendpoint übergeben werden:

`GET /oauth/v2/token?client_id=CLIENT_ID&client_secret=CLIENT_SECRET&grant_type=password&username=USERNAME&password=USERPWD`

Response

```json
{
  "access_token": "ZmJiMTJlMTZlZWQ3M2NmMDNkMTliNTIyMTgwZjFmNzk1YzZjODg0NWE3MTAyMjgwZGUxODAwZjk0MGY3MDlmNw",
  "expires_in": 3600,
  "token_type": "bearer",
  "scope": null,
  "refresh_token": "MTUwYWI2YTViNjllOTA1MzcyYjkzMjc5NGJkNmM5Y2ZhOTQ5NTYzMjE1ZjBjYTYwZGRkMGMzY2EyMDM0OGVkYg"
}
```

Der Bearer-Token muss jedem Request, welches  dem Pattern `ˆ\cv` folgt, übergeben werden.

Mit dem Refresh Token kann ein neues Token angefordert werden:

`GET /oauth/v2/token?client_id=CLIENT_ID&client_secret=CLIENT_SECRET&grant_type=refresh_token&refresh_token=REFRESH_TOKEN`


# User Management

### Nutzer anlegen

Ein Nutzer wird über die `/register` Route angelegt, welche im Gegensatz zur `/cv/execute` Route public ist. 

Bei einem erfolgreichen Request kommt der Code 201 Created zurück, bei Duplikaten 409 Conflict, ansonsten 500.

```json
{
    "parameters": {
        "id": "a942ed44-fc9e-431b-8c01-31aaecd91689",
        "firstName": "Lorem",
        "lastName": "Ipsum",
        "email": "testus@test.de",
        "password": "test123"
    }
}
```

### Passwort vergessen

Passwörter können jederzeit neu angefordert werden. Dazu muss dieser Endpoint genutzt werden:

`GET /reset_password/user/{email}`

Dadurch wird ein Reset-Token erzeugt und eine Mail mit dem Link zum ändern verschickt.

Nun muss das neue Passwort an die API mitsamt dem Token gesendet werden:

`POST /reset_password/confirm/{token}`
````json
{
   "parameters": {
      "password": "new_pwd"
   }
}
````


# API Routes

API ist eine HTTP-RPC baiserte Webschnittstelle. Es wird das [eos ComView Projekt](https://github.com/eosnewmedia/php-com-view-server) als Library genutzt.
POST Requests werden als Commands interpretiert, GET Requests sind Views, die durch Query Parameter eingeschränkt werden.

Für die folgenden Requests gilt:

- ist der Request ein Command, wird dieses per POST an `/cv/execute` gesendet
- jeder Request auf `^/cv$` benötigt ein valides Token

Aktueller authentifizierter Nutzer

GET `/cv/me`

```json
{
  "user": {
    "id": "1942ed44-fc9e-431b-8c01-31aaecd91689",
    "username": "qwer@test.de"
  }
}
```

Nutzer Patchen

```json
{
    "1": {
        "command": "updateUser",
        "parameters": {
            "id": "1242ed44-fc9e-431b-8c01-31aaecd91689",
            "firstName": "Anderer Name",
            "lastName": "Nachname"
        }
    }
}
```

GET /showUserById [id]

`/cv/showUserById?parameters[id]=1242ed44-fc9e-431b-8c01-31aaecd91689`

Response:

```json
{
  "parameters": {
    "id": "1242ed44-fc9e-431b-8c01-31aaecd91689"
  },
  "pagination": [],
  "orderBy": null,
  "data": {
    "firstName": "Anderer Name",
    "lastName": "Nachname",
    "email": "test@test.de"
  }
}
```


Eintrag anlegen/patchen

```json
{
  "1": {
	"command": "createEntry",
	  "parameters": {
            "id": "5542ed44-fc9e-431b-8c01-31aaecd91689",
            "name": "Test",
            "amount": -7.2,
            "date": "2017-08-17",
            "category": "Lebensmittel"
	}
  }
}
```

Vertrag anlegen/patchen


```json
{
  "1": {
	"command": "createContract",
	  "parameters": {
            "id": "1142ed44-fc9e-431b-8c01-31aaecd91689",
            "name": "Testvertrag",
            "amount": -19.99,
            "startDate": "2019-03-05",
            "endDate": "2019-03-05",
            "interval": "monthly",
            "category": "Unterhaltung"
		}
	}
}
```


/getOverviewWithinInterval [start, end]

`/cv/getOverviewWithinInterval?parameters[start]=2019-03-03&parameters[end]=2019-03-06`

```json
{
  "parameters": {
    "start": "2019-03-01",
    "end": "2019-03-06"
  },
  "pagination": [],
  "orderBy": null,
  "data": {
    "entries": [
      {
        "name": "Was anderes",
        "amount": -7.2,
        "date": "2019-03-05",
        "category": "Miete"
      },
      {
        "name": "Test",
        "amount": 82.2,
        "date": "2019-03-05",
        "category": "Lebensmittel"
      }
    ],
    "contracts": [
      {
        "name": "Netflix",
        "amount": -9.95,
        "startDate": "2019-03-05",
        "endDate": "2019-03-05",
        "interval": "monthly",
        "category": "Unterhaltung"
      },
      {
        "name": "Testvertrag",
        "amount": -19.99,
        "startDate": "2019-03-05",
        "endDate": "2019-03-05",
        "interval": "quartal",
        "category": "Sonstiges"
      },
      {
        "name": "Gehalt",
        "amount": 240,
        "startDate": "2019-03-05",
        "endDate": "2019-03-05",
        "interval": "monthly",
        "category": "Gehalt"
      }
    ],
    "graphData": {
      "income": {
        "Lebensmittel": 82.2,
        "Gehalt": 240
      },
      "expenses": {
        "Miete": -7.2,
        "Unterhaltung": -9.95,
        "Sonstiges": -19.99
      }
    },
    "meta": {
      "calculatedBalance": 285.06,
      "totalIncome": 322.2,
      "totalExpenses": -37.14
    }
  }
}
```

GET /getBillingIntervals 

```json
{
  "parameters": [],
  "pagination": [],
  "orderBy": null,
  "data": {
    "monthly": [
      {
        "name": "Netflix",
        "amount": -9.95,
        "startDate": "2019-03-05",
        "endDate": "2019-03-05",
        "category": "Unterhaltung"
      },
      {
        "name": "Gehalt",
        "amount": 240,
        "startDate": "2019-03-05",
        "endDate": "2019-03-05",
        "category": "Gehalt"
      }
    ],
    "quartal": [
      {
        "name": "Testvertrag",
        "amount": -19.99,
        "startDate": "2019-03-05",
        "endDate": "2019-03-05",
        "category": "Sonstiges"
      }
    ]
  }
}
```


# Deployment

## Anforderungen

Der Webserver muss diesen Anforderungen entsprechen:

- PHP 7.2
- MySQL 5.7
- Composer
- Apache 2
- PHP-fpm

## Ablauf

Zum Deployen muss zuerst dieses Projekt via Git geklont werden

```bash
git clone https://github.com/pguetschow/project_04.git
```

Danach die Dependencies mit Composer installieren

```bash
composer install
```

In der .env müssen die Parameter für die Datenbankverbindung und den Mailer angegeben werden:

```dotenv
DATABASE_URL=mysql://user:pwd@host:port/database

MAILER_URL=smtp://user:pwd@host:port/?auth_mode=cram-md5&weitererParam=...
```

Um die Datenbank zu konfigurieren, muss diese erstellt werden und die aktuelle Migration aufgespielt werden:

```bash
 php bin/console doctrine:database:create --if-not-exists
 
 php bin/console doctrine:migrations:migrate
```

Für den Web Client wird ein OAuth2 Client benötigt:

```bash
php bin/console fos:oauth-server:create-client  --grant-type=password --grant-type=refresh_token
```

Danach kann die Anwednung gestartet werden:

```bash
php bin/console server:start
```
