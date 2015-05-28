### Listando las dependencias:
Las dependencias identificadas y agregadas deben seguir el siguiente esquema json

 {
            "name":"Stub1",# Nombre
            "domain":"StubDomain",#Dominio
            "version":"1.2", #Version Opcional
            "bundlename":"juan", #Nombre del bundle dependiente
            "optional":"true", # Si es una dependencia opcional, por defecto en false
            "editable":"false", # Si es de solo lectura, por defecto en true
            "properties":[
                {
                    "stubname":"string",#Tipos de datos convencionales
                    "stubid":"integer",
                    "stubci":"string",
                    "stubdate":"datetime",
                    "stuburi":"uri" #Es una url
                }
            ]
  }

 Los servicios identificados y agregados deben seguir el siguiente esquema json

 {
             "name":"Stub1",# Nombre
             "domain":"StubDomain",#Dominio
             "version":"1.2", #Version Opcional
             "bundlename":"juan", #Nombre del bundle que brinda el servicio
             "optional":"true", # Si es una dependencia opcional, por defecto en false
             "editable":"false", # Si es de solo lectura, por defecto en true
             "uri":"http://app/rest/stub1",
             "properties":[
                 {
                     "stubname":"string",#Tipos de datos convencionales
                     "stubid":"integer",
                     "stubci":"string",
                     "stubdate":"datetime",
                     "stuburi":"uri" #Es una url
                 }
             ]
   }


   RFC 4627[rfc-4627.txt]
   JsonSchema [jsonschema.txt]

   Los servicios son insensibles a cambios de capitalizacion, lo que significa que infoName es lo mismo que
   INFONAME  e INFOname.