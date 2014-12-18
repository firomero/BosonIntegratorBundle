### Listando las dependencias:
Las dependencias identificadas y agregadas deben seguir el siguiente esquema xml

<?xml version="1.0" encoding="utf-8"?>
            <xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
                <xs:element name="dependecy">
                    <xs:complexType>
                        <xs:sequence>
                            <xs:element name="name" type="xs:string"/>
                            <xs:element name="version" type="xs:string"/>
                            <xs:element name="uri" type="xs:string"/>
                            <xs:element name="depends" minOccurs="0" maxOccurs="unbounded">
                                <xs:complexType>
                                    <xs:sequence>
                                        <xs:element name="name" type="xs:string"/>
                                        <xs:element name="version" type="xs:date"/>
                                        <xs:element name="uri" type="xs:string"/>
                                    </xs:sequence>
                                </xs:complexType>
                            </xs:element>
                        </xs:sequence>
                        <xs:attribute name="isbn" type="xs:string"/>
                    </xs:complexType>
                </xs:element>
            </xs:schema>